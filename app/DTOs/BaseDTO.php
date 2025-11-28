<?php

namespace App\DTOs;

abstract class BaseDTO
{
    public static function fromArray(array $data): static
    {
        $reflection = new \ReflectionClass(static::class);

        return $reflection->newInstanceArgs(
            array_map(
                fn ($p) => $data[$p->getName()] ?? null,
                $reflection->getConstructor()->getParameters()
            )
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
