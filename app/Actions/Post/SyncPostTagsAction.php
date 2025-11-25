<?php

namespace App\Actions\Post;

use App\Models\Post;

class SyncPostTagsAction
{
    public function __invoke(Post $post, array $tagIds): void
    {
        $post->tags()->sync($tagIds);
    }
}
