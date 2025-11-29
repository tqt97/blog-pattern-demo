<?php

namespace App\DTOs\Domains\Post;

use Carbon\Carbon;
use Illuminate\Http\Request;

class PostDTO
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
        public readonly array $tags = [],
    ) {}

    /**
     * Create DTO from array (FormRequest validated input).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            slug: $data['slug'] ?? null,
            excerpt: $data['excerpt'] ?? null,
            content: $data['content'],
            status: $data['status'],
            published_at: self::parseNullableDate($data['published_at'] ?? null),
            category_id: $data['category_id'] ?? null,
            thumbnail: $data['thumbnail'] ?? null,
            meta_title: $data['meta_title'] ?? null,
            meta_description: $data['meta_description'] ?? null,
            user_id: $data['user_id'] ?? null, // thường sẽ inject ở Action
            tags: $data['tags'] ?? [],
        );
    }

    /**
     * Create DTO from Request (shortcut).
     */
    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->validated());
    }

    /**
     * Convert DTO to array for repository update/store.
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'category_id' => $this->category_id,
            'thumbnail' => $this->thumbnail,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'user_id' => $this->user_id,
            'tags' => $this->tags,
        ];
    }

    /**
     * Helper: parse Carbon or null.
     */
    private static function parseNullableDate(?string $value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }

    /**
     * Convenience method: create DTO for update (nullable fields supported).
     */
    public static function fromPartial(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            slug: $data['slug'] ?? null,
            excerpt: $data['excerpt'] ?? null,
            content: $data['content'] ?? '',
            status: $data['status'] ?? 'draft',
            published_at: self::parseNullableDate($data['published_at'] ?? null),
            category_id: $data['category_id'] ?? null,
            thumbnail: $data['thumbnail'] ?? null,
            meta_title: $data['meta_title'] ?? null,
            meta_description: $data['meta_description'] ?? null,
            user_id: $data['user_id'] ?? null,
            tags: $data['tags'] ?? [],
        );
    }
}
