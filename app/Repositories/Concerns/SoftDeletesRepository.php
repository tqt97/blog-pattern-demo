<?php

namespace App\Repositories\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;

trait SoftDeletesRepository
{
    protected function assertSoftDeletable(): void
    {
        if (! in_array(SoftDeletes::class, class_uses_recursive($this->model))) {
            throw new \LogicException(static::class.' expects model to use SoftDeletes.');
        }
    }

    public function restore(int $id): void
    {
        $this->assertSoftDeletable();

        $this->query()
            ->onlyTrashed()
            ->whereKey($id)
            ->restore();
    }

    public function forceDelete(int $id): void
    {
        $this->assertSoftDeletable();

        $this->query()
            ->withTrashed()
            ->whereKey($id)
            ->forceDelete();
    }

    public function bulkDelete(array $ids): void
    {
        $this->assertSoftDeletable();

        $this->query()
            ->whereIn($this->model->getKeyName(), $ids)
            ->delete();
    }

    public function bulkRestore(array $ids): void
    {
        $this->assertSoftDeletable();

        $this->query()
            ->onlyTrashed()
            ->whereIn($this->model->getKeyName(), $ids)
            ->restore();
    }

    public function bulkForceDelete(array $ids): void
    {
        $this->assertSoftDeletable();

        $this->query()
            ->withTrashed()
            ->whereIn($this->model->getKeyName(), $ids)
            ->forceDelete();
    }
}
