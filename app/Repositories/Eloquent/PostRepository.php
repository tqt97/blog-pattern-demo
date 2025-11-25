<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Post\PostFilter;
use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
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

    public function paginate(PostFilter $filter, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query();

        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', "%{$filter->search}%")
                    ->orWhere('content', 'like', "%{$filter->search}%");
            });
        }

        if ($filter->categoryId) {
            $query->where('category_id', $filter->categoryId);
        }

        if ($filter->status) {
            $query->where('status', $filter->status);
        }

        return $query
            ->orderBy($filter->orderBy ?? 'created_at', $filter->direction ?? 'desc')
            ->paginate($perPage);
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
}
