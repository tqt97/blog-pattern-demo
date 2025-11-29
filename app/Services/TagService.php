<?php

namespace App\Services;

use App\DTOs\Domains\Tag\TagDTO;
use App\DTOs\Domains\Tag\TagFilterDTO;
use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TagService
{
    public function __construct(
        protected TagRepositoryInterface $tagRepository,
    ) {}

    public function list(TagFilterDTO $filter): LengthAwarePaginator
    {
        return $this->tagRepository->paginate($filter);
    }

    public function create(TagDTO $dto): Tag
    {
        return $this->tagRepository->create($dto->toArray());
    }

    public function update(int $id, TagDTO $dto): Tag
    {
        return $this->tagRepository->update($id, $dto->toArray());
    }

    public function delete(int $id): void
    {
        $this->tagRepository->delete($id);
    }
}
