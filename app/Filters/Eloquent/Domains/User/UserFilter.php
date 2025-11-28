<?php

namespace App\Filters\Eloquent\Domains\User;

use App\Filters\Eloquent\Base\EloquentFilters;

class UserFilter extends EloquentFilters
{
    protected array $searchable = ['name', 'email'];

    protected array $sortable = ['created_at'];

    protected ?string $dateColumn = 'created_at';

    protected function allowed(): array
    {
        return array_merge(parent::allowed(), ['role']);
    }

    public function role(string $value): void
    {
        $this->whereEquals('role', $value);
    }
}
