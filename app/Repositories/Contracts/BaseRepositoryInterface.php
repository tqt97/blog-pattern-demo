<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function query(): Builder;

    // public function all(array $columns = ['*']): Collection;

    // public function paginate(
    //     int $perPage = 15,
    //     array $columns = ['*'],
    //     string $pageName = 'page',
    //     ?int $page = null
    // ): LengthAwarePaginator;

    public function find(int|string $id, array $columns = ['*']): ?Model;

    public function findOrFail(int|string $id, array $columns = ['*']): Model;

    public function findManyByIds(array $ids, bool $withTrashed = false): Collection;

    public function save(Model $model): bool;

    // public function findBy(string $column, mixed $value, array $columns = ['*']): ?Model;

    // public function findWhere(array $conditions, array $columns = ['*']): Collection;

    public function create(array $attributes): Model;

    public function update(int|string $id, array $attributes): Model;

    public function delete(int|string $id): bool;

    //     public function forceDelete(int|string $id): bool;

    //     public function exists(int|string $id): bool;
}
