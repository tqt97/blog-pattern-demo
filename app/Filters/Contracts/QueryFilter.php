<?php

namespace App\Filters\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface QueryFilter
{
    public function apply(Builder $builder, array $data): Builder;
}
