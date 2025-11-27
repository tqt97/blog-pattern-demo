<?php

namespace App\Services;

use App\Actions\Post\CreatePostAction;
use App\Actions\Post\PublishPostAction;
use App\Actions\Post\UpdatePostAction;
use App\Cache\Domains\PostCache;
use App\DTOs\Post\PostDTO;
use App\DTOs\Post\PostFilter;
use App\Exceptions\PostException;
use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class PostService
{
    public function __construct(
        protected PostRepositoryInterface $postRepository,
        protected CreatePostAction $createPostAction,
        protected UpdatePostAction $updatePostAction,
        protected PublishPostAction $publishPostAction,
        protected PostCache $postCache,
    ) {}

    public function list(PostFilter $filter, int $perPage = 15): LengthAwarePaginator
    {
        return $this->postCache->rememberList(
            $filter,
            $perPage,
            fn () => $this->postRepository->paginate($filter, $perPage)
        );
    }

    public function getBySlug(string $slug): Post
    {
        return $this->postCache->rememberShowBySlug($slug, function () use ($slug) {
            $post = $this->postRepository->findBySlug($slug);

            if (! $post) {
                throw PostException::notFound($slug);
            }

            // nếu cần chỉ load published ở frontend:
            // if (!$post->is_published) { // || $post->published_at > now()
            //     throw PostException::notFound($slug);
            // }

            return $post;
        });
    }

    public function findById(int $id): Post
    {
        $post = $this->postRepository->find($id);

        if (! $post) {
            throw PostException::notFound((string) $id);
        }

        return $post;
    }

    public function create(PostDTO $dto): Post
    {
        return ($this->createPostAction)($dto);
    }

    public function update(int $id, PostDTO $dto): Post
    {
        return ($this->updatePostAction)($id, $dto);
    }

    public function publish(int $id, ?Carbon $at = null): Post
    {
        return ($this->publishPostAction)($id, $at);
    }

    public function delete(int $id): void
    {
        $post = $this->postRepository->find($id);

        if (! $post) {
            throw PostException::notFound((string) $id);
        }

        $this->postRepository->delete($id);
    }

    public function topViewed(int $limit = 5): Collection
    {
        return $this->postCache->rememberSidebarTopViewed(5, function () {
            return $this->postRepository->topViewed(5);
        });
    }

    public function recentPublished(): Collection
    {
        return $this->postCache->rememberSidebarRecent(5, function () {
            return $this->postRepository->recentPublished(5);
        });
    }
}
