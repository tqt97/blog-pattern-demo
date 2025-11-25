<?php

namespace App\Cache\Domains;

use App\Cache\Contracts\NamespacedCacheStoreInterface;
use App\DTOs\Post\PostFilter;

class PostCacheBK
{
    public function __construct(
        protected NamespacedCacheStoreInterface $store,
    ) {}

    protected function namespace(): string
    {
        // namespace cho list bài viết
        return 'posts:list';
    }

    protected function ttl()
    {
        return now()->addMinutes(10);
    }

    protected function makeListSuffixKey(PostFilter $filter, int $perPage): string
    {
        $page = request()->integer('page', 1);

        return md5(json_encode([
            'filters' => $filter->toArray(),
            'page' => $page,
            'perPage' => $perPage,
        ]));
    }

    public function rememberList(PostFilter $filter, int $perPage, \Closure $resolver)
    {
        return $this->store->remember(
            $this->namespace(),
            $this->makeListSuffixKey($filter, $perPage),
            $this->ttl(),
            $resolver
        );
    }

    public function flushList(): void
    {
        $this->store->flushNamespace($this->namespace());
    }
}
