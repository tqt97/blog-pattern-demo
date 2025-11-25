<?php

namespace App\DTOs\Post;

class PostFilter
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?int $categoryId = null,
        public readonly ?string $status = null,
        public readonly string $orderBy = 'created_at',
        public readonly string $direction = 'desc',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            categoryId: $data['category_id'] ?? null,
            status: $data['status'] ?? null,
            orderBy: $data['order_by'] ?? 'created_at',
            direction: $data['direction'] ?? 'desc',
        );
    }
}
