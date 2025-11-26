<?php

namespace App\Filters\Domains\Post;

use App\Filters\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

class PostExtraFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $value, array $options = []): Builder
    {
        // TODO: Implement apply() method.
        return $query;
    }
}
