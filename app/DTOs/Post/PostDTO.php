<?php

namespace App\DTOs\Post;

use App\Enums\PostStatus;
use Carbon\CarbonImmutable;

class PostDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $slug,
        public readonly string $content,
        public readonly ?int $categoryId,
        public readonly ?int $userId,
        public readonly array $tagIds,
        // public readonly ?CarbonImmutable $publishedAt,
        public readonly PostStatus $status,
    ) {}

    /**
     * Creates a new instance of the PostDto from an array of data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            slug: $data['slug'],
            content: $data['content'],
            categoryId: $data['category_id'] ?? null,
            userId: $data['user_id'] ?? null,
            tagIds: $data['tag_ids'] ?? [],
            status: isset($data['status']) ? PostStatus::from($data['status']) : PostStatus::DRAFT,
        );
    }

    /**
     * Returns an associative array representation of the DTO.
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'category_id' => $this->categoryId,
            'user_id' => $this->userId,
            'tag_ids' => $this->tagIds,
            'status' => $this->status,
        ];
    }
}
