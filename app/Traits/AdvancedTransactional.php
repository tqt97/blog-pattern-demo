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
     * Example:
     * $this->inTransactionWithIsolation(function () {
     *     // ... logic create / update ...
     * }, 'SERIALIZABLE', attempts: 3);
     *
     * @see https://dev.mysql.com/doc/refman/8.0/en/innodb-locking-reads.html
     *
     * @param  string  $isolation  READ COMMITTED | REPEATABLE READ | SERIALIZABLE
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
        $originalIsolationRow = DB::selectOne('SELECT @@SESSION.transaction_isolation AS isolation');
        $originalIsolation = $originalIsolationRow->isolation ?? null;

        $isolation = $this->normalizeIsolation($isolation);

        $attempt = 0;

        while (true) {
            $attempt++;

            try {
                DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL {$isolation}");

                DB::beginTransaction();

                $result = $callback();

                DB::commit();

                return $result;
            } catch (Throwable $e) {
                DB::rollBack();

                if (! $this->shouldRetryTransaction($e, $attempt, $attempts)) {
                    if ($originalIsolation) {
                        DB::statement(
                            'SET SESSION TRANSACTION ISOLATION LEVEL '.$this->normalizeIsolation($originalIsolation)
                        );
                    }

                    throw $e;
                }

                if ($sleepMilliseconds > 0) {
                    usleep($sleepMilliseconds * 1000 * $attempt);
                }
            } finally {
                if ($originalIsolation) {
                    DB::statement(
                        'SET SESSION TRANSACTION ISOLATION LEVEL '.$this->normalizeIsolation($originalIsolation)
                    );
                }
            }
        }
    }

    /**
     * Run callback in a transaction (default isolation of session),
     * usually used for create / update.
     *
     * Example:
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
        return $this->inTransactionWithIsolation(
            $callback,
            isolation: $this->getCurrentSessionIsolation(),
            attempts: $attempts,
            sleepMilliseconds: $sleepMilliseconds,
        );
    }

    protected function shouldRetryTransaction(Throwable $e, int $attempt, int $maxAttempts): bool
    {
        if ($attempt >= $maxAttempts) {
            return false;
        }

        if (! $e instanceof QueryException) {
            return false;
        }

        $sqlState = $e->getCode();           // SQLSTATE ('40001', '40P01')
        $errorInfo = $e->errorInfo ?? [];     // [sqlState, driverErrorCode, message]

        // Some driver-specific error codes
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

    protected function getCurrentSessionIsolation(): string
    {
        $row = DB::selectOne('SELECT @@SESSION.transaction_isolation AS isolation');

        return $this->normalizeIsolation($row->isolation ?? 'REPEATABLE READ');
    }

    protected function normalizeIsolation(string $isolation): string
    {
        // MySQL returns isolation level as: READ-UNCOMMITTED, READ-COMMITTED, REPEATABLE-READ, SERIALIZABLE
        $isolation = strtoupper(str_replace('-', ' ', $isolation));

        return match ($isolation) {
            'READ UNCOMMITTED',
            'READ COMMITTED',
            'REPEATABLE READ',
            'SERIALIZABLE' => $isolation,
            default => 'REPEATABLE READ', // fallback
        };
    }
}
