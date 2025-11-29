<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Domains\Category\CategoryFilterDTO;
use App\Filters\Eloquent\Domains\Category\CategoryFilter;
use App\Models\Category;
use App\Repositories\Concerns\FilterableRepository;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    use FilterableRepository;

    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function filterClass(): string
    {
        return CategoryFilter::class;
    }

    public function paginate(CategoryFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->query()->select(['id', 'name', 'description', 'created_at']);
        $query = $this->applyFilters($query, $filter->toArray());

        return $query->paginate($filter->perPage);
    }
}
