<?php

namespace App\Filters\Commons;

use App\Filters\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

class EqualsFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $value, array $options = []): Builder
    {
        $column = $options['column'] ?? null;

        if (! $column) {
            return $query;
        }

        return $query->where($column, $value);
    }
}
