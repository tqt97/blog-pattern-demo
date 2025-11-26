<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Post\PostFilterDTO;
use App\Filters\Domains\PostFilters;
use App\Models\Post;
use App\Repositories\Concerns\FilterableRepository;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    use FilterableRepository;

    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function filterClass(): string
    {
        return PostFilters::class;
    }

    protected function publishedQuery(): Builder
    {
        return $this->query()
            ->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    public function findBySlug(string $slug): ?Post
    {
        return $this->query()
            ->where('slug', $slug)
            ->first();
    }

    public function paginatePublished(int $perPage = 15): LengthAwarePaginator
    {
        return $this->publishedQuery()
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    public function paginate(PostFilterDTO $filter, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query()->with('category');
        $query = $this->applyFilters($query, $filter->toArray());

        return $query->paginate($perPage);
    }

    public function findByIdForUpdate(int $id): ?Post
    {
        return $this->query()
            ->whereKey($id)
            ->lockForUpdate()
            ->first();
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = $this->query()
            ->where('slug', $slug)
            ->sharedLock();

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        return $query->exists();
    }

    public function exists(int $id): bool
    {
        return $this->query()->whereKey($id)->exists();
    }

    public function incrementViewCount(int $id): void
    {
        $this->query()->whereKey($id)->increment('view_count');
    }

    public function topViewed(int $limit = 5): Collection
    {
        return $this->publishedQuery()
            ->orderByDesc('view_count')
            ->limit($limit)
            ->get();
    }

    public function recentPublished(int $limit = 5): Collection
    {
        return $this->publishedQuery()
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}
