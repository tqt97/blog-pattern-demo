<?php

namespace App\Filters\Domains;

use App\Filters\Concerns\HasCommonFilters;
use App\Filters\QueryFilters;

class TagFilters extends QueryFilters
{
    use HasCommonFilters;

    protected array $searchable = ['name', 'slug'];

    public function search(string $value): void
    {
        $this->whereLike($this->searchable, $value);
    }
}
