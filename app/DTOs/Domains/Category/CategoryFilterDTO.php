<?php

namespace App\DTOs\Domains\Category;

use App\DTOs\BaseDTO;

class CategoryFilterDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $search,
        public readonly ?string $sort,
        public readonly ?string $direction,
        public readonly ?int $perPage = 15,
    ) {}
}
