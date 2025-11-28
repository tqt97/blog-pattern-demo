<?php

namespace App\Actions\Category;

use App\DTOs\Domains\Category\CategoryDTO;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CreateCategoryAction
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function __invoke(CategoryDTO $dto): Category
    {
        return $this->categoryRepository->create($dto->toArray());
    }
}
