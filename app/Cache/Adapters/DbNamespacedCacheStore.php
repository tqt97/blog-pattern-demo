<?php

namespace App\Cache\Adapters;

use App\Cache\Contracts\NamespacedCacheStoreInterface;
use Closure;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;

class DbNamespacedCacheStore implements NamespacedCacheStoreInterface
{
    public function __construct(
        protected ?string $store = null
    ) {}

    protected function repo()
    {
        return Cache::store($this->store ?? config('cache.default'));
    }

    protected function versionKey(string $namespace): string
    {
        return $namespace.':version';
    }

    protected function getVersion(string $namespace): int
    {
        $key = $this->versionKey($namespace);

        return (int) $this->repo()->rememberForever($key, fn () => 1);
    }

    protected function bumpVersion(string $namespace): void
    {
        $key = $this->versionKey($namespace);

        $this->repo()->increment($key);
    }

    public function remember(string $namespace, string $suffixKey, DateTimeInterface|int $ttl, Closure $resolver)
    {
        $version = $this->getVersion($namespace);

        $fullKey = $namespace.':v'.$version.':'.$suffixKey;

        return $this->repo()->remember($fullKey, $ttl, $resolver);
    }

    public function flushNamespace(string $namespace): void
    {
        $this->bumpVersion($namespace);
    }
}
