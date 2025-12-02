<?php

namespace App\Services;

use App\DTOs\Domains\Upload\UploadedFileDTO;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{
    public function rulesFor(string $type): array
    {
        $config = $this->configForType($type);

        return $config['rules'] ?? ['required', 'image', 'max:4096'];
    }

    public function upload(UploadedFile $file, string $type): UploadedFileDTO
    {
        $config = $this->configForType($type);

        $disk = $config['disk'] ?? 'public';
        $folder = $config['folder'] ?? 'uploads/others';

        $filename = $this->generateSeoFilename($file);

        $relativePath = $file->storeAs($folder, $filename, $disk);

        $url = Storage::disk($disk)->url($relativePath);

        return new UploadedFileDTO(
            path: $url,
            relative: $relativePath,
            filename: $filename,
            disk: $disk,
            original: $file->getClientOriginalName(),
            type: $type,
        );
    }

    public function delete(string $path, string $type): bool
    {
        $config = $this->configForType($type);
        $disk = $config['disk'] ?? 'public';

        $relative = $this->normalizePath($path);

        if (! $relative) {
            return false;
        }

        return Storage::disk($disk)->delete($relative);
    }

    protected function configForType(string $type): array
    {
        $types = config('upload.types', []);
        $default = $types['default'] ?? [
            'disk' => 'public',
            'folder' => 'uploads/others',
            'rules' => ['required', 'image', 'max:4096'],
        ];

        return $types[$type] ?? $default;
    }

    protected function generateSeoFilename(UploadedFile $file): string
    {
        $originalName = $file->getClientOriginalName(); // maybe is "blob"
        $nameOnly = $originalName === 'blob'
            ? ''
            : pathinfo($originalName, PATHINFO_FILENAME);

        $extension = $file->getClientOriginalExtension()
            ?: $file->extension()
            ?: 'jpg';

        $slug = Str::slug($nameOnly);

        if ($slug === '') {
            $slug = 'image';
        }

        $suffix = Str::lower(Str::random(6));

        return "{$slug}-{$suffix}.{$extension}";
    }

    protected function normalizePath(string $path): ?string
    {
        if ($path === '') {
            return null;
        }

        // full URL -> parse path
        if (str_contains($path, '://')) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path = $parsed ?: $path;
        }

        $relative = Str::of($path)
            ->replace('/storage/', '')
            ->replace('storage/', '')
            ->ltrim('/');

        return $relative === '' ? null : (string) $relative;
    }
}
