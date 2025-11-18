<?php

namespace App\Traits;

trait HasSlugRouteKey
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
