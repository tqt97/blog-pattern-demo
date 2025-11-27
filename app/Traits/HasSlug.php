<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

trait HasSlug
{
    protected static bool $useSlugAsRouteKey = false;

    protected static bool $slugUniqueAcrossSoftDeleted = true;

    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            static::setSlugOnModel($model);
        });

        static::updating(function (Model $model) {
            $slugColumn = $model->getSlugColumn();
            $sourceColumn = $model->getSlugSourceColumn();

            if (
                method_exists($model, 'isDirty')
                && ! $model->isDirty($slugColumn)
                && ! $model->isDirty($sourceColumn)
            ) {
                return;
            }

            static::setSlugOnModel($model);
        });
    }

    protected static function setSlugOnModel(Model $model): void
    {
        $slugColumn = $model->getSlugColumn(); // default 'slug'
        $sourceColumn = $model->getSlugSourceColumn(); // default 'name'

        if (! empty($model->{$slugColumn})) {
            $baseSlug = Str::slug($model->{$slugColumn});
        } else {
            $sourceValue = $model->{$sourceColumn} ?? '';

            if ($sourceValue === '') {
                return;
            }

            $baseSlug = Str::slug($sourceValue);
        }

        $uniqueSlug = static::makeUniqueSlug($model, $slugColumn, $baseSlug);

        $model->{$slugColumn} = $uniqueSlug;
    }

    protected static function makeUniqueSlug(Model $model, string $slugColumn, string $baseSlug): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : Str::random(8);
        $originalSlug = $slug;
        $i = 1;

        while (static::slugExists($model, $slugColumn, $slug)) {
            $slug = $originalSlug.'-'.$i;
            $i++;
        }

        return $slug;
    }

    protected static function slugExists(Model $model, string $slugColumn, string $slug): bool
    {
        $query = static::query()->where($slugColumn, $slug);

        if ($model->exists) {
            $query->whereKeyNot($model->getKey());
        }

        if (
            in_array(SoftDeletes::class, class_uses_recursive($model), true)
            && ! static::$slugUniqueAcrossSoftDeleted
        ) {
            $query->whereNull($model->getQualifiedDeletedAtColumn());
        }

        return $query->exists();
    }

    public function getSlugSourceColumn(): string
    {
        if (property_exists($this, 'slugFrom') && ! empty(static::$slugFrom)) {
            return static::$slugFrom;
        }

        return 'name';
    }

    public function getSlugColumn(): string
    {
        if (property_exists($this, 'slugColumn') && ! empty(static::$slugColumn)) {
            return static::$slugColumn;
        }

        return 'slug';
    }

    public function getRouteKeyName(): string
    {
        if (static::$useSlugAsRouteKey) {
            return $this->getSlugColumn();
        }

        return parent::getRouteKeyName();
    }
}
