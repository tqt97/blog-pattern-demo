<?php

namespace App\DTOs\Post;

use App\Enums\PostSortable;
use App\Enums\SortDirection;

class PostFilterDTO
{
    public function __construct(
        public readonly ?string $search,
        public readonly ?int $category_id,
        public readonly ?string $status, // 'draft', 'pending', 'published'
        public readonly ?int $author_id,
        public readonly bool $onlyPublished, // cho frontend
        public readonly ?PostSortable $sort_by,
        public readonly ?SortDirection $direction,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            category_id: isset($data['category_id']) ? (int) $data['category_id'] : null,
            status: $data['status'] ?? null,
            author_id: isset($data['author_id']) ? (int) $data['author_id'] : null,
            onlyPublished: (bool) ($data['only_published'] ?? true),
            sort_by: isset($data['sort_by'])
            ? PostSortable::fromNullable($data['sort_by'])
            : null,
            direction: isset($data['direction'])
            ? SortDirection::fromNullable($data['direction'])
            : null,
        );
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'author_id' => $this->author_id,
            'only_published' => $this->onlyPublished,
            'sort_by' => $this->sort_by?->value,
            'direction' => $this->direction?->value,
        ];
    }
}
