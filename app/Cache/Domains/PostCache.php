<?php

namespace App\Cache\Domains;

use App\Cache\Contracts\NamespacedCacheStoreInterface;
use Closure;
use DateTimeInterface;

class PostCache
{
    public function __construct(
        protected NamespacedCacheStoreInterface $store,
    ) {}

    protected function ttl(): DateTimeInterface|int
    {
        // tuỳ gu: 10 phút, hoặc cấu hình riêng
        return now()->addMinutes(10);
    }

    /** ------- NAMESPACE helpers ------- */
    protected function listNamespace(): string
    {
        return 'posts:list';
    }

    protected function showNamespace(): string
    {
        return 'posts:show';
    }

    protected function sidebarNamespace(): string
    {
        return 'posts:sidebar';
    }

    /** ------- LIST (index) ------- */
    public function rememberList(array $filter, int $perPage, Closure $resolver)
    {
        $suffixKey = $this->makeListSuffixKey($filter, $perPage);

        return $this->store->remember(
            $this->listNamespace(),
            $suffixKey,
            $this->ttl(),
            $resolver
        );
    }

    public function flushList(): void
    {
        $this->store->flushNamespace($this->listNamespace());
    }

    protected function makeListSuffixKey(array $filter, int $perPage): string
    {
        $page = request()->integer('page', 1);

        return md5(json_encode([
            'filters' => $filter,
            'page' => $page,
            'perPage' => $perPage,
        ]));
    }

    /** ------- SHOW (single post, theo slug) ------- */

    /**
     * Cache nội dung 1 bài viết theo slug (thường dùng cho frontend show page).
     */
    public function rememberShowBySlug(string $slug, Closure $resolver)
    {
        // Nếu có đa ngôn ngữ, có thể include locale vào suffix
        $suffixKey = 'slug:'.$slug;

        return $this->store->remember(
            $this->showNamespace(),
            $suffixKey,
            $this->ttl(),
            $resolver
        );
    }

    public function flushShow(): void
    {
        $this->store->flushNamespace($this->showNamespace());
    }

    /** ------- Sidebar (top viewed / recent) ------- */
    public function rememberSidebarTopViewed(int $limit, Closure $resolver)
    {
        $suffixKey = 'top-viewed:limit-'.$limit;

        return $this->store->remember(
            $this->sidebarNamespace(),
            $suffixKey,
            $this->ttl(),
            $resolver
        );
    }

    public function rememberSidebarRecent(int $limit, Closure $resolver)
    {
        $suffixKey = 'recent:limit-'.$limit;

        return $this->store->remember(
            $this->sidebarNamespace(),
            $suffixKey,
            $this->ttl(),
            $resolver
        );
    }

    public function flushSidebar(): void
    {
        $this->store->flushNamespace($this->sidebarNamespace());
    }

    /** ------- tiện: flush all liên quan Post ------- */
    public function flushAll(): void
    {
        $this->flushList();
        $this->flushShow();
        $this->flushSidebar();
    }
}
