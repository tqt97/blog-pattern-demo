<?php

namespace App\Jobs;

use App\Cache\Domains\PostCache;
use App\DTOs\Post\PostFilter;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class PrewarmPostCacheJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PostCache $cache, PostRepositoryInterface $postRepository): void
    {
        // 1. Prewarm list trang 1 với filter mặc định
        $filter = new PostFilter; // hoặc PostFilter::default()

        $cache->rememberList(
            $filter,
            15,
            fn () => $postRepository->paginate($filter, 15)
        );

        // 2. Prewarm sidebar
        $cache->rememberSidebarTopViewed(
            5,
            fn () => $postRepository->topViewed(5)
        );

        $cache->rememberSidebarRecent(
            5,
            fn () => $postRepository->recentPublished(5)
        );
    }
}
