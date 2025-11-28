<?php

namespace App\Filters\Eloquent\Domains\Category;

use App\Filters\Eloquent\Base\EloquentFilters;

class CategoryFilter extends EloquentFilters
{
    protected array $searchable = ['name', 'description'];

    protected array $sortable = ['name', 'created_at'];

    // /**
    //  * Allow key which can be filtered
    //  *
    //  * @return array<string>
    //  */
    // protected function allowed(): array
    // {
    //     return [
    //         'search',
    //         'sort',
    //         'direction',
    //         'to_date',
    //     ];
    // }

    /**
     * Map filter name to method
     */
    // protected function map(): array
    // {
    //     return [
    //         // 'from_date' => 'createdFrom',
    //         // 'to_date' => 'createdTo',
    //     ];
    // }

    // public function search(string $value): void
    // {
    //     $this->whereLike($this->searchable, $value);
    // }

    // public function createdFrom(string $value): void
    // {
    //     $this->whereDateFrom('created_at', $value);
    // }

    // public function createdTo(string $value): void
    // {
    //     $this->whereDateTo('created_at', $value);
    // }

    // public function sort(string $value): void
    // {
    //     $this->applySort(
    //         sortBy: $value,
    //         direction: $this->data['direction'] ?? 'desc',
    //         allowed: $this->sortable,
    //         default: 'created_at'
    //     );
    // }
}
