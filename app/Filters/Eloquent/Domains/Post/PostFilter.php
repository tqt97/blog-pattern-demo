<?php

namespace App\Filters\Eloquent\Domains\Post;

use App\Filters\Eloquent\Base\EloquentFilters;

class PostFilter extends EloquentFilters
{
    protected array $searchable = ['title'];

    protected array $sortable = ['title', 'published_at', 'created_at', 'status'];

    protected ?string $dateColumn = 'published_at';

    protected function allowed(): array
    {
        return array_merge(parent::allowed(), [
            'status',
            'category_id',
            'user_id',
            'tag_id',
            'trashed',
        ]);
    }

    protected function map(): array
    {
        return array_merge(parent::map(), [
            'user_id' => 'author',
            'category_id' => 'category',
            'tag_id' => 'tag',
            'trashed' => 'trashed',
        ]);
    }

    public function status(string $value): void
    {
        $this->whereEquals('status', $value);
    }

    public function category(int $value): void
    {
        $this->whereEquals('category_id', $value);
    }

    public function author(int $value): void
    {
        $this->whereEquals('user_id', $value);
    }

    public function tag(int $value): void
    {
        $this->whereRelationEquals('tags', 'id', $value);
    }

    public function trashed(string $value): void
    {
        if (! in_array($value, ['only', 'with'], true)) {
            return;
        }

        $this->applyTrashed($value);
    }
}
