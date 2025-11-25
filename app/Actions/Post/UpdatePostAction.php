<?php

namespace App\Actions\Post;

use App\DTOs\Post\PostDTO;
use App\Exceptions\PostException;
use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Traits\AdvancedTransactional;
use App\Traits\Transactional;

class UpdatePostAction
{
    use AdvancedTransactional;
    use Transactional;

    public function __construct(
        protected PostRepositoryInterface $postRepository,
        protected SyncPostTagsAction $syncPostTagsAction,
    ) {}

    public function __invoke(int $id, PostDTO $dto): Post
    {
        return $this->lockAndExecute(function () use ($id, $dto): Post|null {
            $post = $this->postRepository->findByIdForUpdate($id);

            if (! $post) {
                throw PostException::notFound((string) $id);
            }

            if ($post->slug !== $dto->slug && $this->postRepository->slugExists($dto->slug, $post->id)) {
                throw PostException::slugExists($dto->slug);
            }

            $post->update($dto->toArray());

            if (! empty($dto->tagIds)) {
                ($this->syncPostTagsAction)($post, $dto->tagIds);
            }

            return $post->fresh();
        }, 3, 100);
    }
}
