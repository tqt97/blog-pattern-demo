<?php

namespace App\Filters\Eloquent\Domains\Tag;

use App\Filters\Eloquent\Base\EloquentFilters;

class TagFilter extends EloquentFilters
{
    protected array $searchable = ['name', 'description'];

    protected array $sortable = ['name', 'created_at'];
}
