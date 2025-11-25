<?php

namespace App\Cache\Adapters;

use App\Cache\Contracts\NamespacedCacheStoreInterface;
use Closure;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;

class RedisNamespacedCacheStore implements NamespacedCacheStoreInterface
{
    public function __construct(
        protected ?string $store = null // null = dùng cache.default
    ) {}

    protected function repo()
    {
        return Cache::store($this->store ?? config('cache.default'));
    }

    public function remember(string $namespace, string $suffixKey, DateTimeInterface|int $ttl, Closure $resolver)
    {
        $key = $namespace.':'.$suffixKey;

        return $this->repo()->tags([$namespace])->remember($key, $ttl, $resolver);
    }

    public function flushNamespace(string $namespace): void
    {
        $this->repo()->tags([$namespace])->flush();
    }
}
