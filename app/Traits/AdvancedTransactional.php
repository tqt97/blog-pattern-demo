<?php

namespace App\Traits;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

trait AdvancedTransactional
{
    /**
     * Run callback in a transaction with custom isolation level.
     *
     * Ví dụ:
     * $this->inTransactionWithIsolation(function () {
     *     // ... logic create / update ...
     * }, 'SERIALIZABLE', attempts: 3);
     *
     * @see https://dev.mysql.com/doc/refman/8.0/en/innodb-locking-reads.html
     *
     * @param  string  $isolation  READ COMMITTED | REPEATABLE READ | SERIALIZABLE
     * @param  int  $attempts  Số lần retry khi gặp deadlock / serialization failure
     * @param  int  $sleepMilliseconds  Thời gian ngủ giữa các lần retry (ms)
     * @return mixed
     *
     * @throws Throwable
     */
    protected function inTransactionWithIsolation(
        Closure $callback,
        string $isolation = 'REPEATABLE READ',
        int $attempts = 1,
        int $sleepMilliseconds = 50,
    ) {
        // Lấy isolation hiện tại (MySQL 8: transaction_isolation)
        // Nếu cần compat MySQL cũ có thể dùng @@tx_isolation.
        $originalIsolationRow = DB::selectOne('SELECT @@SESSION.transaction_isolation AS isolation');
        $originalIsolation = $originalIsolationRow->isolation ?? null;

        $attempt = 0;

        while (true) {
            $attempt++;

            try {
                // Đặt isolation BEFORE transaction
                DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL {$isolation}");

                DB::beginTransaction();

                $result = $callback();

                DB::commit();

                return $result;
            } catch (Throwable $e) {
                DB::rollBack();

                if (! $this->shouldRetryTransaction($e, $attempt, $attempts)) {
                    // Restore isolation trước khi quăng exception
                    if ($originalIsolation) {
                        DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL {$originalIsolation}");
                    }

                    throw $e;
                }

                // Backoff nhẹ giữa các lần retry
                if ($sleepMilliseconds > 0) {
                    // Nhân với attempt cho exponential backoff nhẹ
                    usleep($sleepMilliseconds * 1000 * $attempt);
                }

                // Lặp để retry lần tiếp theo
            } finally {
                // Sau khi thành công hoặc hết attempt, restore isolation
                if ($originalIsolation) {
                    DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL {$originalIsolation}");
                }
            }
        }
    }

    /**
     * Run callback in a transaction (mặc định isolation của session),
     * thường dùng với các query đã tự lock bằng lockForUpdate() bên trong callback.
     *
     * Ví dụ:
     * $this->lockAndExecute(function () {
     *     $post = $this->postRepository->query()
     *         ->whereKey($id)
     *         ->lockForUpdate()
     *         ->first();
     *     // ...
     * });
     *
     * @return mixed
     *
     * @throws Throwable
     */
    protected function lockAndExecute(
        Closure $callback,
        int $attempts = 1,
        int $sleepMilliseconds = 50,
    ) {
        // Giữ isolation hiện tại, chỉ thêm retry logic
        return $this->inTransactionWithIsolation(
            $callback,
            isolation: $this->getCurrentSessionIsolation(),
            attempts: $attempts,
            sleepMilliseconds: $sleepMilliseconds,
        );
    }

    /**
     * Quyết định có retry transaction hay không dựa trên loại lỗi + số attempt.
     */
    protected function shouldRetryTransaction(Throwable $e, int $attempt, int $maxAttempts): bool
    {
        if ($attempt >= $maxAttempts) {
            return false;
        }

        if (! $e instanceof QueryException) {
            return false;
        }

        $sqlState = $e->getCode();           // SQLSTATE (ví dụ: '40001', '40P01')
        $errorInfo = $e->errorInfo ?? [];     // [sqlState, driverErrorCode, message]

        // Một số mã quen thuộc:
        // 40001: serialization failure (MySQL, Postgres)
        // 40P01: deadlock detected (Postgres)
        $retryableStates = ['40001', '40P01'];

        // Driver-specific error codes (MySQL, SQL Server...)
        // 1213: deadlock (MySQL)
        // 1205: lock wait timeout (SQL Server)
        $retryableDriverCodes = [1213, 1205];

        if (in_array($sqlState, $retryableStates, true)) {
            return true;
        }

        if (isset($errorInfo[1]) && in_array((int) $errorInfo[1], $retryableDriverCodes, true)) {
            return true;
        }

        return false;
    }

    /**
     * Helper: lấy isolation hiện tại của session.
     */
    protected function getCurrentSessionIsolation(): string
    {
        $row = DB::selectOne('SELECT @@SESSION.transaction_isolation AS isolation');

        return $row->isolation ?? 'REPEATABLE-READ';
    }
}
