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

    protected function whereRelationEquals(string $relation, string $column, int|string $value): void
    {
        $this->builder->whereHas($relation, function ($q) use ($column, $value) {
            $q->where($column, $value);
        });
    }

    protected function whereRelationIn(string $relation, string $column, array $values): void
    {
        $this->builder->whereHas($relation, function ($q) use ($column, $values) {
            $q->whereIn($column, $values);
        });
    }

    protected function whereRelationDateFrom(string $relation, string $column, string $value): void
    {
        $from = now()->parse($value)->startOfDay();
        $this->builder->whereHas($relation, function ($q) use ($column, $from) {
            $q->where($column, '>=', $from);
        });
    }

    protected function applyTrashed(?string $value): void
    {
        if ($value === 'only') {
            $this->builder->onlyTrashed();
        } elseif ($value === 'with') {
            $this->builder->withTrashed();
        }
    }

    protected function onlyTrashed(): void
    {
        $this->builder->onlyTrashed();
    }

    protected function withTrashed(): void
    {
        $this->builder->withTrashed();
    }
}
