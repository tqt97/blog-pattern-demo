<?php

namespace App\DTOs\Domains\Category;

use App\DTOs\BaseDTO;

class CategoryFilterDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $q,
        public readonly ?string $sort,
        public readonly ?string $direction,
        public readonly ?int $perPage,
    ) {}

    // public static function fromArray(array $data): self
    // {
    //     return new self(
    //         search: $data['search'] ?? null,
    //         sort: $data['sort'] ?? null,
    //         direction: $data['direction'] ?? null,
    //         perPage: $data['per_page'] ?? null,
    //     );
    // }
    // public function toArray(): array
    // {
    //     return [
    //         'search' => $this->search,
    //         'sort' => $this->sort,
    //         'direction' => $this->direction,
    //         'per_page' => $this->perPage
    //     ];
    // }
}
