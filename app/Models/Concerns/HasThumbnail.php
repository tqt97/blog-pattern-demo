<?php

namespace App\Models\Concerns;

trait HasThumbnail
{
    protected function getThumbnailColumn(): string
    {
        return 'thumbnail';
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        $column = $this->getThumbnailColumn();

        return media_url($this->{$column});
    }
}
