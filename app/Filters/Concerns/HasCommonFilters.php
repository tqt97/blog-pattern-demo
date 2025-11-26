<?php

namespace App\Filters\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasCommonFilters
{
    protected array $searchable = [];

    protected function whereLike(array $columns, string $value): void
    {
        if ($value === '') {
            return;
        }

        $this->builder->where(function (Builder $q) use ($columns, $value) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', "%{$value}%");
            }
        });
    }

    protected function whereEquals(string $column, mixed $value): void
    {
        $this->builder->where($column, $value);
    }

    protected function whereDateFrom(string $column, string $value): void
    {
        $this->builder->whereDate($column, '>=', $value);
    }

    protected function whereDateTo(string $column, string $value): void
    {
        $this->builder->whereDate($column, '<=', $value);
    }

    protected function applySort(
        string $sortBy,
        string $direction,
        array $allowed,
        string $default
    ): void {
        $column = in_array($sortBy, $allowed, true) ? $sortBy : $default;
        $dir = $direction === 'asc' ? 'asc' : 'desc';

        $this->builder->orderBy($column, $dir);
    }
}
