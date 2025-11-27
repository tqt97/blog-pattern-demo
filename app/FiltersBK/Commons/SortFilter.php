<?php

namespace App\Filters\Commons;

use App\Filters\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

class SortFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $value, array $options = []): Builder
    {
        $allowed = $options['allowed'] ?? [];
        $default = $options['default'] ?? 'created_at';
        $directionKey = $options['direction_key'] ?? 'sort_direction';
        $data = $options['data'] ?? [];

        $column = in_array($value, $allowed, true) ? $value : $default;
        $direction = ($data[$directionKey] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($column, $direction);
    }
}
