<?php

use App\Supports\Media;

if (! function_exists('media_url')) {
    function media_url(?string $path): ?string
    {
        return Media::url($path);
    }
}

if (! function_exists('media_path')) {
    function media_path(?string $path): ?string
    {
        return Media::path($path);
    }
}
