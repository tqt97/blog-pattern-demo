<?php

namespace App\Services;

use App\Cache\Domains\PostCache;
use App\DTOs\Domains\Post\PostDTO;
use App\Enums\PostStatus;
use App\Events\PostCreated;
use App\Events\PostPublished;
use App\Exceptions\PostException;
use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Traits\AdvancedTransactional;
use App\Traits\Transactional;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PostService
{
    use AdvancedTransactional;
    use Transactional;

    public function __construct(
        protected PostRepositoryInterface $postRepository,
        protected PostCache $postCache,
        protected UploadService $uploadService
    ) {}

    public function list(array $filter): LengthAwarePaginator
    {
        // $perPage = $filters['per_page'] ?? Post::getPerPage();
        return $this->postRepository->paginate($filter);
        // return $this->postCache->rememberList(
        //     $filter,
        //     $perPage,
        //     fn () => $this->postRepository->paginate($filter)
        // );
    }

    public function getBySlug(string $slug): Post
    {
        return $this->postCache->rememberShowBySlug($slug, function () use ($slug) {
            $post = $this->postRepository->findBySlug($slug);

            if (! $post) {
                throw PostException::notFound($slug);
            }

            // if ($post->status !== PostStatus::PUBLISHED) {
            //     throw PostException::notFound($slug);
            // }

            return $post;
        });
    }

    public function create(PostDTO $dto): Post
    {
        return $this->inTransactionWithIsolation(function () use ($dto) {
            $post = $this->postRepository->create($dto->toArray(['tag_ids']));

            if (! empty($dto->tag_ids)) {
                $this->syncTags($post, $dto->tag_ids);
            }

            DB::afterCommit(function () use ($post) {
                event(new PostCreated($post));
            });

            return $post;
        }, 'SERIALIZABLE', 3, 100);
    }

    public function update(Post $post, PostDTO $dto): Post
    {
        return $this->lockAndExecute(function () use ($post, $dto): Post|null {

            $data = $dto->toArray(['tag_ids']);

            if ($data['thumbnail'] === null) {
                unset($data['thumbnail']);
            }

            $oldThumbnail = $post->thumbnail;
            $newThumbnail = $data['thumbnail'] ?? $oldThumbnail;

            $this->postRepository->update($post->id, $data);

            if (! empty($dto->tag_ids)) {
                $this->syncTags($post, $dto->tag_ids);
            }

            DB::afterCommit(function () use ($newThumbnail, $oldThumbnail) {
                if (
                    $oldThumbnail &&
                    $newThumbnail &&
                    $oldThumbnail !== $newThumbnail
                ) {
                    $this->uploadService->delete($oldThumbnail, 'posts');
                }
            });

            return $post->fresh();
        }, 3, 100);
    }

    public function delete(Post $post): void
    {
        $this->postRepository->delete($post->id);
    }

    public function restore(Post $post): Post
    {
        return $this->inTransaction(function () use ($post) {
            if (method_exists($post, 'restore')) {
                $this->postRepository->restore($post->id);

                return $post->refresh();
            }
        });
    }

    public function forceDelete(Post $post): void
    {
        $this->inTransaction(function () use ($post) {
            if ($post->thumbnail) {
                $this->uploadService->delete($post->thumbnail, 'posts');
            }

            $this->postRepository->forceDelete($post->id);
        });
    }

    public function publish(Post $post, ?Carbon $publishedAt = null): Post
    {
        return $this->inTransaction(function () use ($post, $publishedAt) {
            $wasPublished = $post->status === PostStatus::PUBLISHED;

            $post->status = PostStatus::PUBLISHED;

            if (! $post->published_at) {
                $post->published_at = $publishedAt ?: now();
            }

            $this->postRepository->save($post);

            $justPublished = ! $wasPublished && $post->status === PostStatus::PUBLISHED;

            DB::afterCommit(function () use ($post, $justPublished) {
                if ($justPublished) {
                    PostPublished::dispatch($post);
                }
            });

            return $post;
        });
    }

    public function unpublish(Post $post): Post
    {
        return DB::transaction(function () use ($post) {
            $post->status = PostStatus::DRAFT;
            $this->postRepository->save($post);

            return $post;
        });
    }

    public function bulkPublish(array $ids, ?Carbon $publishAt = null): void
    {
        $posts = $this->postRepository->findManyByIds($ids);

        foreach ($posts as $post) {
            $this->publish($post, $publishAt);
        }
    }

    public function bulkUnpublish(array $ids): void
    {
        $posts = $this->postRepository->findManyByIds($ids);

        foreach ($posts as $post) {
            $this->unpublish($post);
        }
    }

    public function bulkDelete(array $ids): void
    {
        $this->postRepository->bulkDelete($ids);
    }

    public function bulkRestore(array $ids): void
    {
        $this->postRepository->bulkRestore($ids);
    }

    public function bulkForceDelete(array $ids): void
    {
        $this->postRepository->bulkForceDelete($ids);
    }

    public function syncTags(Post $post, array $tagIds): void
    {
        $post->tags()->sync(array_filter($tagIds));
    }

    public function topViewed(int $limit = 5): Collection
    {
        return $this->postCache->rememberSidebarTopViewed($limit, function () use ($limit) {
            return $this->postRepository->topViewed($limit);
        });
    }

    public function recentPublished(int $limit): Collection
    {
        return $this->postCache->rememberSidebarRecent($limit, function () use ($limit) {
            return $this->postRepository->recentPublished($limit);
        });
    }
}
