<?php

namespace App\Filters\Eloquent\Base;

use App\Filters\Eloquent\Concerns\HasCommonFilters;

abstract class EloquentFilters extends QueryFilters
{
    use HasCommonFilters;

    /**
     * Những cột cho phép search LIKE
     * Ví dụ: ['name', 'description']
     */
    protected array $searchable = [];

    /**
     * Những cột cho phép sort
     */
    protected array $sortable = [];

    /**
     * Cột ngày mặc định để filter from/to
     */
    protected ?string $dateColumn = 'created_at';

    protected function allowed(): array
    {
        return [
            'search',
            'sort',
            'direction',
            'from_date',
            'to_date',
        ];
    }

    /**
     * Return an associative array of filter names to methods.
     *
     * This method is used to map filter names to methods that should be
     * called when the filter is applied.
     */
    protected function map(): array
    {
        return [
            'from_date' => 'dateFrom',
            'to_date' => 'dateTo',
        ];
    }

    /**
     * Search records by LIKE operator.
     *
     * @param  string|null  $value  The value to search.
     *
     * The columns to search by can be changed by setting the $searchable
     * property.
     */
    public function search(?string $value): void
    {
        if (empty($this->searchable)) {
            return;
        }

        $this->whereLike($this->searchable, $value);
    }

    /**
     * Filter records by sorting them in ascending or descending order.
     *
     * @param  string|null  $value  The column to sort by.
     */
    public function sort(?string $value): void
    {
        if (empty($this->sortable)) {
            return;
        }

        $this->applySort(
            sortBy: $value ?? '',
            direction: $this->data['direction'] ?? 'desc',
            allowed: $this->sortable,
            default: $this->sortable[0] ?? 'created_at'
        );
    }

    /**
     * Filter records by date greater than or equal to given value.
     *
     * The date column to filter by can be changed by setting the $dateColumn
     * property.
     *
     * @param  string  $value  The date value to filter by.
     */
    public function dateFrom(string $value): void
    {
        if (! $this->dateColumn) {
            return;
        }

        $this->whereDateFrom($this->dateColumn, $value);
    }

    /**
     * Filter records by date less than or equal to given value.
     *
     * @param  string  $value  The date value to filter by.
     */
    public function dateTo(string $value): void
    {
        if (! $this->dateColumn) {
            return;
        }

        $this->whereDateTo($this->dateColumn, $value);
    }
}
