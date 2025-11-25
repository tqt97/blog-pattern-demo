<?php

namespace App\Actions\Post;

use App\Exceptions\PostException;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Traits\Transactional;

class IncrementPostViewCountAction
{
    use Transactional;

    public function __construct(
        protected PostRepositoryInterface $postRepository
    ) {}

    public function __invoke(int $id): void
    {
        $this->inTransaction(function () use ($id) {
            $post = $this->postRepository->findByIdForUpdate($id);

            if (! $post) {
                throw PostException::notFound((string) $id);
            }

            $post->views++;
            $post->save();
        });
    }
}
