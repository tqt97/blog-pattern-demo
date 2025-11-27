<?php

namespace App\Traits;

use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

trait Transactional
{
    /**
     * Run code in transaction, rollback on exception.
     */
    protected function inTransaction(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }

    /**
     * Transaction fails, log error and throw exception.
     *
     * @throws Throwable
     */
    protected function safeTransaction(Closure $callback, string $context = ''): mixed
    {
        try {
            return DB::transaction($callback);
        } catch (Throwable $e) {
            logger()->error('Transaction failed', [
                'context' => $context,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
