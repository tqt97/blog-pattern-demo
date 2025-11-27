<?php

namespace App\Filters\Domains;

use App\Filters\Concerns\HasCommonFilters;
use App\Filters\QueryFilters;

class CategoryFilters extends QueryFilters
{
    use HasCommonFilters;

    protected array $searchable = ['name', 'description'];

    protected array $sortable = ['created_at'];

    public function search(string $value): void
    {
        $this->whereLike($this->searchable, $value);
    }

    public function status(string $value): void
    {
        $this->whereEquals('status', $value);
    }

    public function sortBy(string $value): void
    {
        $direction = $this->data['sort_direction'] ?? null;

        $this->applySort(
            sortBy: $value,
            direction: $direction,
            allowed: $this->sortable,
            default: 'created_at'
        );
    }
}
