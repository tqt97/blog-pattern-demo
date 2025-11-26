<?php

namespace App\Repositories\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait FilterableRepository
{
    abstract protected function filterClass(): string;

    protected function applyFilters(
        Builder $query,
        array $data,
    ): Builder {
        $filters = app($this->filterClass());

        return $filters->apply($query, $data);
    }
}
