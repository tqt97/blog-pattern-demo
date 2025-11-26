<?php

namespace App\Filters\Domains;

use App\Enums\PostSortable;
use App\Enums\SortDirection;
use App\Filters\Concerns\HasCommonFilters;
use App\Filters\QueryFilters;

class PostFilters extends QueryFilters
{
    use HasCommonFilters;

    protected array $searchable = ['title', 'excerpt', 'content'];

    protected array $sortable = ['published_at', 'created_at', 'view_count'];

    protected function allowed(): array
    {
        return [
            'search',
            'status',
            'user_id',
            'category_id',
            'from_date',
            'to_date',
            'sort_by',
            'direction', // dùng nội bộ cho sort
        ];
    }

    /**
     * Map filter name to method
     */
    protected function map(): array
    {
        return [
            'author_id' => 'author',
            'category_id' => 'category',
            'from_date' => 'fromDate',
            'to_date' => 'toDate',
            'sort_by' => 'sortBy',
        ];
    }

    // ---- các method filter, tên gắn với key/map ----

    public function search(string $value): void
    {
        $this->whereLike($this->searchable, $value);
    }

    public function status(string $value): void
    {
        $this->whereEquals('status', $value);
    }

    public function author(int $value): void
    {
        $this->whereEquals('user_id', $value);
    }

    public function category(int $value): void
    {
        $this->whereEquals('category_id', $value);
    }

    public function fromDate(string $value): void
    {
        $this->whereDateFrom('published_at', $value);
    }

    public function toDate(string $value): void
    {
        $this->whereDateTo('published_at', $value);
    }

    public function sortBy(string $value): void
    {
        $sortEnum = PostSortable::fromNullable($value);

        $direction = SortDirection::fromNullable(
            $this->data['direction'] ?? null
        );

        $this->applySort(
            sortBy: $sortEnum->value,
            direction: $direction->value,
            allowed: $this->sortable,
            default: 'published_at'
        );
    }
}
