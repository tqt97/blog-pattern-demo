<?php

namespace App\Observers;

use App\Cache\Domains\PostCache;
use App\Jobs\PrewarmPostCacheJob;
use App\Models\Post;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class PostObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(protected PostCache $postCache) {}

    public function created(Post $post): void
    {
        $this->flushAfterCommit();
    }

    public function updated(Post $post): void
    {
        $this->flushAfterCommit();
    }

    public function deleted(Post $post): void
    {
        $this->flushAfterCommit();
    }

    public function restored(Post $post): void
    {
        $this->flushAfterCommit();
    }

    protected function flushAfterCommit(): void
    {
        $this->postCache->flushAll();
        PrewarmPostCacheJob::dispatch();
    }
}
