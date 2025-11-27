<?php

namespace App\Actions\Post;

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
        $this->postRepository->incrementViewCount($id);
    }
}
