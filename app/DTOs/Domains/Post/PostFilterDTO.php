<?php

namespace App\DTOs\Domains\Post;

use App\DTOs\BaseDTO;

class PostFilterDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $q,
        public readonly ?string $sort,
        public readonly ?string $direction,
        public readonly ?int $perPage,
        public readonly ?int $categoryId,
        public readonly ?string $status, // 'draft', 'pending', 'published'
        public readonly ?int $userId,
        public readonly ?int $tagId,
        public readonly ?string $trashed
    ) {}
}
