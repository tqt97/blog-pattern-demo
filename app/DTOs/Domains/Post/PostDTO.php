<?php

namespace App\DTOs\Domains\Post;

use App\DTOs\BaseDTO;
use Carbon\Carbon;

class PostDTO extends BaseDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $slug,
        public readonly ?string $excerpt,
        public readonly string $content,
        public readonly string $status,         // draft | pending | published
        public readonly ?Carbon $published_at,
        public readonly ?int $category_id,
        public readonly ?string $thumbnail,

        // SEO
        public readonly ?string $meta_title,
        public readonly ?string $meta_description,

        // Optional: creator/updater context
        public readonly ?int $user_id = null,

        // tags
        public readonly array $tag_ids = [],
    ) {}
}
