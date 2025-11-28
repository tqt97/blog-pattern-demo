<?php

namespace App\Filters\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property Builder $builder
 */
trait HasCommonFilters
{
    protected function whereLike(array $columns, ?string $value): void
    {
        $value = trim((string) $value);

        if ($value === '') {
            return;
        }

        $this->builder->where(function (Builder $q) use ($columns, $value) {
            foreach ($columns as $i => $column) {
                $method = $i === 0 ? 'where' : 'orWhere';
                $q->{$method}($column, 'LIKE', '%'.$value.'%');
            }
        });
    }

    protected function whereEquals(string $column, mixed $value): void
    {
        $this->builder->where($column, $value);
    }

    protected function whereDateFrom(string $column, string $value): void
    {
        $from = now()->parse($value)->startOfDay();
        $this->builder->where($column, '>=', $from);
    }

    protected function whereDateTo(string $column, string $value): void
    {
        $to = now()->parse($value)->endOfDay();
        $this->builder->where($column, '<=', $to);
    }

    protected function whereIn(string $column, array $values): void
    {
        if ($values === []) {
            return;
        }

        $this->builder->whereIn($column, $values);
    }

    protected function applySort(
        string $sortBy,
        ?string $direction,
        array $allowed,
        string $default
    ): void {
        $column = in_array($sortBy, $allowed, true) ? $sortBy : $default;
        $dir = $direction === 'asc' ? 'asc' : 'desc';

        $this->builder->orderBy($column, $dir);
    }
}
