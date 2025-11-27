<?php

namespace App\Filters;

use App\Filters\Contracts\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilters implements QueryFilter
{
    protected Builder $builder;

    protected array $data = [];

    public function __invoke(Builder $builder, array $data): Builder
    {
        return $this->apply($builder, $data);
    }

    final public function apply(Builder $builder, array $data): Builder
    {
        $this->builder = $builder;
        $this->data = $data;

        $allowed = $this->allowed();

        foreach ($this->data as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (! empty($allowed) && ! in_array($key, $allowed, true)) {
                continue;
            }

            $method = $this->map()[$key] ?? $key;

            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }

        return $this->builder;
    }

    /**
     * Key nào được phép xử lý
     */
    protected function allowed(): array
    {
        return [];
    }

    protected function map(): array
    {
        return [];
    }
}
