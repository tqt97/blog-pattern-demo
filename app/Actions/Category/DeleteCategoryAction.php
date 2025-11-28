<?php

namespace App\Actions\Category;

use App\Repositories\Contracts\CategoryRepositoryInterface;

class DeleteCategoryAction
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function __invoke(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }
}
