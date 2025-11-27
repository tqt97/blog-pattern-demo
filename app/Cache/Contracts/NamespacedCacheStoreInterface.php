<?php

namespace App\Cache\Contracts;

use Closure;
use DateTimeInterface;

interface NamespacedCacheStoreInterface
{
    /**
     * Remember 1 key theo namespace (vd: posts:list).
     *
     * @param  string  $suffixKey  key con (vd: hash filter + page)
     * @return mixed
     */
    public function remember(string $namespace, string $suffixKey, DateTimeInterface|int $ttl, Closure $resolver);

    /**
     * Invalidate toàn bộ cache thuộc 1 namespace.
     */
    public function flushNamespace(string $namespace): void;
}
