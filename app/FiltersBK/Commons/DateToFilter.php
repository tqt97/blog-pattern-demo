<?php

namespace App\Filters\Commons;

use App\Filters\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

class DateToFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $value, array $options = []): Builder
    {
        $column = $options['column'] ?? 'created_at';

        return $query->whereDate($column, '<=', $value);
    }
}
