<?php

namespace App\Filters\Commons;

use App\Filters\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

class SearchFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $value, array $options = []): Builder
    {
        $value = trim((string) $value);

        if ($value === '') {
            return $query;
        }

        $columns = $options['columns'] ?? [];

        if (empty($columns)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($columns, $value) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', "%{$value}%");
            }
        });
    }
}
