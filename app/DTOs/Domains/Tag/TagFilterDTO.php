<?php

namespace App\DTOs\Domains\Tag;

use App\DTOs\BaseDTO;

class TagFilterDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $q,
        public readonly ?string $sort,
        public readonly ?string $direction,
        public readonly ?int $perPage,
    ) {}
}
