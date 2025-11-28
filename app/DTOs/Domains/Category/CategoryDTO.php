<?php

namespace App\DTOs\Domains\Category;

use App\DTOs\BaseDTO;

class CategoryDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $slug,
        public readonly ?string $description,
    ) {}
}
