<?php

namespace App\DTOs;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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

    public function toArray(array $except = []): array
    {
        return Arr::except(get_object_vars($this), $except);
    }

    public function toSnakeArray(): array
    {
        return collect($this->toArray())
            ->mapWithKeys(fn ($value, $key) => [Str::snake($key) => $value])
            ->all();
    }
}
