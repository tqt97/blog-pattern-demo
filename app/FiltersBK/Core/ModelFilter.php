<?php

namespace App\Filters\Core;

use App\Filters\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

class ModelFilter
{
    public function __construct(
        protected Builder $query,
    ) {}

    /**
     * @param  array  $data  dữ liệu filter (từ DTO->toArray())
     * @param  array  $map  map key => class hoặc [class, options]
     */
    public function apply(array $data, array $map): Builder
    {
        foreach ($map as $key => $definition) {
            if (! array_key_exists($key, $data)) {
                continue;
            }

            $value = $data[$key];

            if ($value === null || $value === '') {
                continue;
            }

            $filterClass = $definition;
            $options = [];

            // cho phép truyền dạng:
            // 'search' => [SearchFilter::class, ['columns' => [...]]]
            if (is_array($definition)) {
                $filterClass = $definition[0] ?? null;
                $options = $definition[1] ?? [];
            }

            if (! $filterClass) {
                continue;
            }

            /** @var FilterInterface $filter */
            $filter = app($filterClass);

            $this->query = $filter->apply($this->query, $value, $options);
        }

        return $this->query;
    }
}
