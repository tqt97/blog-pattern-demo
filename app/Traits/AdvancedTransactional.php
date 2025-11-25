<?php

namespace App\Traits;

use Closure;
use Illuminate\Support\Facades\DB;

trait AdvancedTransactional
{
    /**
     * Run transaction with custom isolation level.
     *
     * @see https://dev.mysql.com/doc/refman/8.0/en/innodb-locking-reads.html
     *
     * @isolation READ COMMITTED, REPEATABLE READ, SERIALIZABLE
     *
     * @return mixed
     */
    protected function inTransactionWithIsolation(Closure $callback, string $isolation = 'REPEATABLE READ')
    {
        return DB::transaction(function () use ($callback, $isolation) {
            DB::statement("SET SESSION TRANSACTION ISOLATION LEVEL {$isolation}");

            return $callback();
        });
    }

    /**
     * Run callback but force row-level lock on the model.
     *
     * @see https://dev.mysql.com/doc/refman/8.0/en/innodb-locking-reads.html
     *
     * @return mixed
     */
    protected function lockAndExecute(Closure $callback)
    {
        return DB::transaction(function () use ($callback) {
            return $callback(); // ->with lockForUpdate()
        });
    }
}
