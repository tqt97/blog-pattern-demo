<?php

namespace App\Actions\Post;

use App\Cache\Domains\PostCache;
use App\Models\Post;

class SyncPostTagsAction
{
    public function __construct(
        protected PostCache $postCache,
    ) {}

    public function __invoke(Post $post, array $tagIds): void
    {
        $post->tags()->sync($tagIds);
    }
}
