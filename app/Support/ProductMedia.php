<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ProductMedia
{
    /**
     * URL for <img src> — legacy absolute URLs unchanged; managed paths via public storage.
     */
    public static function publicUrl(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    public static function isManagedRelativePath(?string $path): bool
    {
        if ($path === null || $path === '') {
            return false;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return false;
        }

        $normalized = ltrim($path, '/');

        return Str::startsWith($normalized, 'products/thumbnails/')
            || Str::startsWith($normalized, 'products/gallery/');
    }

    public static function deletePublicFileIfManaged(?string $path): void
    {
        if (! self::isManagedRelativePath($path)) {
            return;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }
}
