<?php

namespace App\Actions\Post;

use App\Cache\Domains\PostCache;
use App\Events\PostPublished;
use App\Exceptions\PostException;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Traits\Transactional;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PublishPostAction
{
    use Transactional;

    public function __construct(
        protected PostRepositoryInterface $postRepository,
        protected PostCache $postCache,
    ) {}

    public function __invoke(int $id, ?Carbon $publishAt = null): Model
    {
        return $this->inTransaction(function () use ($id, $publishAt) {
            $post = $this->postRepository->find($id);

            if (! $post) {
                throw PostException::notFound((string) $id);
            }

            // current state
            $wasPublished = (bool) $post->is_published;

            // Set new state
            $post->is_published = true;

            // if has published_at field => use it; else use current time
            $post->published_at = $post->published_at ?: ($publishAt ?: now());
            $post->save();

            $post->refresh();

            // only dispatch "publish" if just published
            $justPublished = ! $wasPublished && (bool) $post->is_published;

            DB::afterCommit(function () use ($post, $justPublished) {
                if ($justPublished) {
                    PostPublished::dispatch($post);
                }
            });

            return $post;
        });
    }
}
