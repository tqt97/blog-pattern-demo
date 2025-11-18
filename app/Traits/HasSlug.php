<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Bootstrap the trait.
     *
     * Listen for the creating and updating Model events. When either of these events are
     * fired, the setSlugOnModel method is called.
     */
    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            static::setSlugOnModel($model);
        });

        static::updating(function (Model $model) {
            static::setSlugOnModel($model);
        });
    }

    /**
     * Set slug on model.
     */
    protected static function setSlugOnModel(Model $model): void
    {
        $slugColumn = $model->getSlugColumn();  // default 'slug'
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

        // make sure slug is unique
        $uniqueSlug = static::makeUniqueSlug($model, $slugColumn, $baseSlug);

        $model->{$slugColumn} = $uniqueSlug;
    }

    /**
     * Generate a unique slug for a model.
     */
    protected static function makeUniqueSlug(Model $model, string $slugColumn, string $baseSlug): string
    {
        // if base slug is empty, generate random string
        $slug = $baseSlug !== '' ? $baseSlug : Str::random(8);
        $originalSlug = $slug;
        $i = 1;

        while (static::slugExists($model, $slugColumn, $slug)) {
            $slug = $originalSlug.'-'.$i;
            $i++;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists in the database.
     */
    protected static function slugExists(Model $model, string $slugColumn, string $slug): bool
    {
        $query = static::query()->where($slugColumn, $slug);

        if ($model->exists) {
            $query->whereKeyNot($model->getKey());
        }

        return $query->exists();
    }

    /**
     * Get the column name that will be used to generate slug.
     *
     * It will look for a static property named 'slugFrom' first, and if it exists and is not empty,
     * it will return the value of that property. Otherwise, it will return 'name' as the default value.
     */
    public function getSlugSourceColumn(): string
    {
        if (property_exists($this, 'slugFrom') && ! empty(static::$slugFrom)) {
            return static::$slugFrom;
        }

        return 'name';
    }

    /**
     * Get the column name that will be used to store the generated slug.
     *
     * It will look for a static property named 'slugColumn' first, and if it exists and is not empty,
     * it will return the value of that property. Otherwise, it will return 'slug' as the default value.
     */
    public function getSlugColumn(): string
    {
        if (property_exists($this, 'slugColumn') && ! empty(static::$slugColumn)) {
            return static::$slugColumn;
        }

        return 'slug';
    }

    /**
     * Get the route key name for the model.
     *
     * This method returns the value of the column name that is used to store the generated slug.
     * This value is used to generate the route key name for the model. For example, if the column name
     * is 'slug', the route key name will be 'slug'.
     */
    public function getRouteKeyName(): string
    {
        return $this->getSlugColumn();
    }
}
