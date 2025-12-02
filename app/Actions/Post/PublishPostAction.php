<?php

namespace App\Actions\Post;

use App\Cache\Domains\PostCache;
use App\Enums\PostStatus;
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

            $wasPublished = $post->status instanceof PostStatus
                ? $post->status === PostStatus::PUBLISHED
                : $post->status === PostStatus::PUBLISHED->value;

            // Set trạng thái mới
            $post->status = PostStatus::PUBLISHED;

            if (! $post->published_at) {
                $post->published_at = $publishAt ?: now();
            }

            $post->save();
            $post->refresh();

            $isNowPublished = $post->status instanceof PostStatus
                ? $post->status === PostStatus::PUBLISHED
                : $post->status === PostStatus::PUBLISHED->value;

            $justPublished = ! $wasPublished && $isNowPublished;

            DB::afterCommit(function () use ($post, $justPublished) {
                if ($justPublished) {
                    PostPublished::dispatch($post);
                }
            });

            return $post;
        });
    }
}
