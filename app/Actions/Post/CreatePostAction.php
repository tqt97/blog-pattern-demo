<?php

namespace App\Actions\Post;

use App\Cache\Domains\PostCache;
use App\DTOs\Post\PostDTO;
use App\Events\PostCreated;
use App\Exceptions\PostException;
use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Traits\AdvancedTransactional;
use App\Traits\Transactional;
use Illuminate\Support\Facades\DB;

class CreatePostAction
{
    use AdvancedTransactional;
    use Transactional;

    public function __construct(
        protected PostRepositoryInterface $postRepository,
        protected SyncPostTagsAction $syncPostTagsAction,
        protected PostCache $postCache,
    ) {}

    public function __invoke(PostDTO $dto): Post
    {
        return $this->inTransactionWithIsolation(function () use ($dto) {
            if ($this->postRepository->slugExists($dto->slug)) {
                throw PostException::slugExists($dto->slug);
            }

            $post = $this->postRepository->create($dto->toArray());

            if (! empty($dto->tags)) {
                ($this->syncPostTagsAction)($post, $dto->tags);
            }

            DB::afterCommit(function () use ($post) {
                event(new PostCreated($post));
            });

            return $post;
        }, 'SERIALIZABLE', 3, 100);
    }
}
