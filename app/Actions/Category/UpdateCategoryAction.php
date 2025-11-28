<?php

namespace App\Actions\Category;

use App\DTOs\Domains\Category\CategoryDTO;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class UpdateCategoryAction
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function __invoke(int $id, CategoryDTO $dto): Category
    {
        return $this->categoryRepository->update($id, $dto->toArray());
    }
}
