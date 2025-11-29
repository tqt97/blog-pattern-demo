<?php

namespace App\DTOs;

abstract class BaseDTO
{
    public static function fromArray(array $data): static
    {
        $reflection = new \ReflectionClass(static::class);
        $params = $reflection->getConstructor()->getParameters();

        $args = [];

        foreach ($params as $p) {
            $name = $p->getName();                          // perPage
            $snake = \Illuminate\Support\Str::snake($name); // per_page

            if (array_key_exists($name, $data)) {
                $value = $data[$name];
            } elseif (array_key_exists($snake, $data)) {
                $value = $data[$snake];
            } elseif ($p->isDefaultValueAvailable()) {
                $value = $p->getDefaultValue();
            } else {
                $value = null;
            }

            $args[] = $value;
        }

        return $reflection->newInstanceArgs($args);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
