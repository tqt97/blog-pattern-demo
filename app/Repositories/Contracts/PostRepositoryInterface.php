<?php

namespace App\Repositories\Contracts;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?Post;

    public function paginate(array $filter): LengthAwarePaginator;

    public function findByIdForUpdate(int $id): ?Post;

    public function slugExists(string $slug, ?int $ignoreId = null): bool;

    public function exists(int $id): bool;

    public function incrementViewCount(int $id): void;

    public function topViewed(int $limit = 5): Collection;

    public function recentPublished(int $limit = 5): Collection;

    public function bulkDelete(array $ids): void;

    public function bulkRestore(array $ids): void;

    public function bulkForceDelete(array $ids): void;

    public function restore(int $id): void;

    public function forceDelete(int $id): void;
}
