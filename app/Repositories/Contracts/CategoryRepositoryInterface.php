<?php

namespace App\Repositories\Contracts;

use App\DTOs\Domains\Category\CategoryFilterDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate(CategoryFilterDTO $filter, int $perPage = 15): LengthAwarePaginator;
}
