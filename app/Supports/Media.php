<?php

namespace App\Supports;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media
{
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $appUrl = config('app.url');
        $appHost = parse_url($appUrl, PHP_URL_HOST);

        // full URL?
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $host = parse_url($path, PHP_URL_HOST);

            // external other domain app → return as is
            if ($host && $appHost && $host !== $appHost) {
                return $path;
            }

            // internal: http(s)://app/storage/...
            $publicBase = rtrim($appUrl, '/').'/storage/';

            if (Str::startsWith($path, $publicBase)) {
                $relative = Str::after($path, $publicBase);

                return Storage::disk('public')->url($relative);
            }

            return $path;
        }

        // relative → storage public
        return Storage::disk('public')->url($path);
    }

    public static function path(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        // If URL → convert internal path
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);
            $thumbHost = parse_url($path, PHP_URL_HOST);

            if ($thumbHost === $appHost) {
                $relative = Str::after($path, '/storage/');

                return Storage::disk('public')->path($relative);
            }

            return null;
        }

        return Storage::disk('public')->path($path);
    }
}
