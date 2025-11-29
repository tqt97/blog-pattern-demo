<?php

namespace App\Filters\Eloquent\Domains\Post;

use App\Filters\Eloquent\Base\EloquentFilters;

class PostFilter extends EloquentFilters
{
    protected array $searchable = ['title', 'excerpt', 'content'];

    protected array $sortable = ['published_at', 'created_at'];

    protected ?string $dateColumn = 'published_at';

    protected function allowed(): array
    {
        return array_merge(parent::allowed(), [
            'status',
            'category_id',
        ]);
    }

    public function status(string $value): void
    {
        $this->whereEquals('status', $value);
    }

    public function category_id(int|string $value): void
    {
        $this->whereEquals('category_id', $value);
    }
}
