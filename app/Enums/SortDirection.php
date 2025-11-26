<?php

namespace App\Enums;

enum SortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    public static function values(): array
    {
        return [
            self::ASC->value,
            self::DESC->value,
        ];
    }

    public static function fromNullable(?string $value): self
    {
        return $value === 'asc' ? self::ASC : self::DESC;
    }
}
