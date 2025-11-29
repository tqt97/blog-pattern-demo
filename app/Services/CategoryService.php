<?php

namespace App\Services;

use App\DTOs\Domains\Category\CategoryDTO;
use App\DTOs\Domains\Category\CategoryFilterDTO;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function list(CategoryFilterDTO $filter): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($filter);
    }

    public function create(CategoryDTO $dto): Category
    {
        return $this->categoryRepository->create($dto->toArray());
    }

    public function update(int $id, CategoryDTO $dto): Category
    {
        return $this->categoryRepository->update($id, $dto->toArray());
    }

    public function delete(int $id): void
    {
        $this->categoryRepository->delete($id);
    }
}
