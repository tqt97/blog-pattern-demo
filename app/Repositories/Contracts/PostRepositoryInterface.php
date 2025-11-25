<?php

namespace App\Repositories\Contracts;

use App\DTOs\Post\PostFilter;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface extends BaseRepositoryInterface
{
    // public function paginatePublished(int $perPage = 15): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Post;

    public function paginate(PostFilter $filter, int $perPage = 15): LengthAwarePaginator;

    public function findByIdForUpdate(int $id): ?Post;

    public function slugExists(string $slug, ?int $ignoreId = null): bool;

    public function exists(int $id): bool;

    public function incrementViewCount(int $id): void;

    public function topViewed(int $limit = 5): Collection;

    public function recentPublished(int $limit = 5): Collection;
}
