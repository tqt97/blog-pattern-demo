<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Domains\Tag\TagFilterDTO;
use App\Filters\Eloquent\Domains\Tag\TagFilter;
use App\Models\Tag;
use App\Repositories\Concerns\FilterableRepository;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TagRepository extends BaseRepository implements TagRepositoryInterface
{
    use FilterableRepository;

    public function __construct(Tag $model)
    {
        parent::__construct($model);
    }

    public function filterClass(): string
    {
        return TagFilter::class;
    }

    public function paginate(TagFilterDTO $filter): LengthAwarePaginator
    {
        $query = $this->query()->select(['id', 'name', 'created_at']);
        $query = $this->applyFilters($query, $filter->toArray());

        return $query->paginate($filter->perPage);
    }
}
