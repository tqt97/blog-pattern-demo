<?php

namespace App\Repositories\Eloquent;

use App\Enums\PostStatus;
use App\Filters\Eloquent\Domains\Post\PostFilter;
use App\Models\Post;
use App\Repositories\Concerns\FilterableRepository;
use App\Repositories\Concerns\SoftDeletesRepository;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    use FilterableRepository;
    use SoftDeletesRepository;

    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function filterClass(): string
    {
        return PostFilter::class;
    }

    protected function publishedQuery(): Builder
    {
        return $this->query()
            ->where('status', PostStatus::PUBLISHED->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function findBySlug(string $slug): ?Post
    {
        return $this->query()
            ->where('slug', $slug)
            ->first();
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = $this->query()
            ->select('id', 'category_id', 'user_id', 'title', 'slug', 'thumbnail', 'status', 'published_at', 'created_at')
            ->with(['category:id,name', 'tags:id,name', 'user:id,name']);
        $query = $this->applyFilters($query, $filters);

        $perPage = $filters['per_page'] ?? null;

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

    public function bulkDelete(array $ids): void
    {
        $this->query()
            ->whereIn('id', $ids)
            ->delete(); // soft delete
    }

    public function bulkRestore(array $ids): void
    {
        $this->query()
            ->onlyTrashed()
            ->whereIn('id', $ids)
            ->restore();
    }

    public function bulkForceDelete(array $ids): void
    {
        $this->query()
            ->withTrashed()
            ->whereIn('id', $ids)
            ->forceDelete();
    }
}
