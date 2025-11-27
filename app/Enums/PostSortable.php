<?php

namespace App\Enums;

enum PostSortable: string
{
    case CREATED_AT = 'created_at';
    case PUBLISHED_AT = 'published_at';
    case VIEW_COUNT = 'view_count';

    public static function values(): array
    {
        return [
            self::CREATED_AT->value,
            self::PUBLISHED_AT->value,
            self::VIEW_COUNT->value,
        ];
    }

    public static function fromNullable(?string $value): self
    {
        return match ($value) {
            'published_at' => self::PUBLISHED_AT,
            'view_count' => self::VIEW_COUNT,
            default => self::CREATED_AT,
        };
    }
}
