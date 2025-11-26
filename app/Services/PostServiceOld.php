<?php

namespace App\Services;

use App\DTOs\Post\PostDTO;
use App\DTOs\Post\PostFilterDTO;
use App\Exceptions\PostException;
use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Traits\AdvancedTransactional;
use App\Traits\Transactional;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PostServiceOld
{
    use AdvancedTransactional;
    use Transactional;

    public function __construct(
        protected PostRepositoryInterface $postRepository
    ) {}

    public function list(PostFilterDTO $filter, int $perPage = 15): LengthAwarePaginator
    {
        return $this->postRepository->paginate($filter, $perPage);
    }

    public function getBySlug(string $slug): Post
    {
        $post = $this->postRepository->findBySlug($slug);

        if (! $post) {
            throw PostException::notFound($slug);
        }

        return $post;
    }

    public function getById(int $id): Post
    {
        $post = $this->postRepository->find($id);

        if (! $post) {
            throw PostException::notFound((string) $id);
        }

        return $post;
    }

    public function create(PostDTO $data)
    {
        return $this->safeTransaction(function () use ($data) {

            if ($this->postRepository->findBySlug($data->slug)) {
                throw PostException::slugExists($data->slug);
            }

            return $this->postRepository->create($data->toArray());

        }, 'post.create');
    }

    public function createAdvanced(PostDTO $data)
    {
        return $this->inTransactionWithIsolation(function () use ($data) {

            // dùng REPEATABLE READ hoặc SERIALIZABLE
            $exists = $this->postRepository->query()
                ->where('slug', $data->slug)
                ->sharedLock()    // bảo đảm không ai ghi khi check
                ->exists();

            if ($exists) {
                throw PostException::slugExists($data->slug);
            }

            $post = $this->postRepository->create($data->toArray());

            if (! empty($data->tags)) {
                $post->tags()->sync($data->tags);
            }

            return $this->postRepository->create($data->toArray());

        }, 'SERIALIZABLE');  // bảo đảm uniqueness tuyệt đối
    }

    public function update(int $id, PostDTO $data)
    {
        $post = $this->postRepository->find($id);
        if (! $post) {
            throw PostException::notFound($id);
        }

        if ($post->slug !== $data->slug && $this->postRepository->findBySlug($data->slug)) {
            throw PostException::slugExists($data->slug);
        }

        return DB::transaction(
            fn () => $this->postRepository->update($id, $data->toArray())
        );
    }

    public function updateAdvanced(int $id, PostDTO $data)
    {
        return $this->lockAndExecute(function () use ($id, $data) {

            $post = $this->postRepository->query()
                ->where('id', $id)
                ->lockForUpdate()      // <—— khoá dòng này
                ->first();

            if (! $post) {
                throw PostException::notFound($id);
            }

            $post->update($data->toArray());

            return $post->fresh();
        });
    }

    public function updateAdvanced2(int $id, PostDTO $dto): Post
    {
        return $this->lockAndExecute(function () use ($id, $dto) {
            /** @var Builder $query */
            $query = $this->postRepository->query();

            /** @var \App\Models\Post|null $post */
            $post = $query
                ->whereKey($id)
                ->lockForUpdate()
                ->first();

            if (! $post) {
                throw PostException::notFound((string) $id);
            }

            // Nếu slug đổi, check trùng với bài khác
            if ($post->slug !== $dto->slug) {
                $slugExists = $this->postRepository
                    ->query()
                    ->where('slug', $dto->slug)
                    ->where('id', '!=', $post->id)
                    ->sharedLock()
                    ->exists();

                if ($slugExists) {
                    throw PostException::slugExists($dto->slug);
                }
            }

            $post->update($dto->toArray());

            if (! empty($dto->tags)) {
                $post->tags()->sync($dto->tags);
            }

            return $post->fresh();
        });
    }

    public function delete(int $id): void
    {
        $post = $this->postRepository->find($id);
        if (! $post) {
            throw PostException::notFound($id);
        }

        DB::transaction(fn () => $this->postRepository->delete($id));
    }

    public function deleteAdvanced(int $id): void
    {
        $this->inTransaction(function () use ($id) {
            $post = $this->postRepository->find($id);

            if (! $post) {
                throw PostException::notFound((string) $id);
            }

            // Nếu anh muốn chắc cú hơn có thể lockForUpdate trước khi delete:
            $this->postRepository
                ->query()
                ->whereKey($post->getKey())
                ->lockForUpdate()
                ->delete();
        });
    }
}
