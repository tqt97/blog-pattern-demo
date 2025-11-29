<?php

namespace App\DTOs\Domains\Tag;

use App\DTOs\BaseDTO;

class TagDTO extends BaseDTO
{
    public function __construct(public readonly ?string $name, public readonly ?string $slug) {}
}
