<?php

namespace App\Actions\Post;

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
    ) {}

    public function __invoke(PostDTO $dto): Post
    {
        return $this->inTransactionWithIsolation(function () use ($dto) {
            if ($this->postRepository->slugExists($dto->slug)) {
                throw PostException::slugExists($dto->slug);
            }

            $post = $this->postRepository->create($dto->toArray());

            if (! empty($dto->tagIds)) {
                ($this->syncPostTagsAction)($post, $dto->tagIds);
            }

            DB::afterCommit(fn () => PostCreated::dispatch($post));

            return $post;
        }, 'SERIALIZABLE', 3, 100);
    }
}
