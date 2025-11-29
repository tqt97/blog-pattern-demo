<?php

namespace App\Repositories\Contracts;

use App\DTOs\Domains\Tag\TagFilterDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TagRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate(TagFilterDTO $filter): LengthAwarePaginator;
}
