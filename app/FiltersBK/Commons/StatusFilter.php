<?php

namespace App\Filters\Commons;

use App\Filters\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

class StatusFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $value, array $options = []): Builder
    {
        $column = $options['column'] ?? 'status';

        return $query->where($column, $value);
    }
}
