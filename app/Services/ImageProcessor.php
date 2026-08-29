<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ImageProcessor
{
    /**
     * Resize and store a single uploaded image, producing a "main" copy and a thumbnail.
     *
     * @return array{image: string, thumbnail: string}
     */
    public static function storeResized(
        UploadedFile $file,
        string $directory,
        int $maxWidth = 1600,
        int $thumbWidth = 480,
        int $quality = 75,
    ): array {
        $manager = new ImageManager(new Driver);

        Storage::disk('public')->makeDirectory($directory);
        Storage::disk('public')->makeDirectory($directory.'/thumbnails');

        // Always emit WebP: ~30% smaller than the JPEG/PNG originals we receive.
        $name = Str::random(24).'.webp';
        $imagePath = $directory.'/'.$name;
        $thumbPath = $directory.'/thumbnails/'.$name;

        try {
            $main = $manager->decodePath($file->getRealPath());
            $main->scaleDown(width: $maxWidth);
            $mainEncoded = $main->encode(new WebpEncoder(quality: $quality));
            Storage::disk('public')->put($imagePath, (string) $mainEncoded);

            $thumb = $manager->decodePath($file->getRealPath());
            $thumb->scaleDown(width: $thumbWidth);
            $thumbEncoded = $thumb->encode(new WebpEncoder(quality: $quality));
            Storage::disk('public')->put($thumbPath, (string) $thumbEncoded);

            return [
                'image' => $imagePath,
                'thumbnail' => $thumbPath,
            ];
        } catch (\Throwable $e) {
            Log::error('ImageProcessor failed, falling back to direct store.', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);

            // Fallback: store original without processing.
            $stored = $file->store($directory, 'public');

            return [
                'image' => $stored,
                'thumbnail' => $stored,
            ];
        }
    }

    /**
     * Compress and resize a single image, returning the stored path (no thumbnail).
     */
    public static function storeCompressed(
        UploadedFile $file,
        string $directory,
        int $maxWidth = 1280,
        int $quality = 72,
    ): string {
        $manager = new ImageManager(new Driver);

        Storage::disk('public')->makeDirectory($directory);

        // Always emit WebP: ~30% smaller than the JPEG/PNG originals we receive.
        $name = Str::random(24).'.webp';
        $imagePath = $directory.'/'.$name;

        try {
            $image = $manager->decodePath($file->getRealPath());
            $image->scaleDown(width: $maxWidth);
            $encoded = $image->encode(new WebpEncoder(quality: $quality));
            Storage::disk('public')->put($imagePath, (string) $encoded);

            return $imagePath;
        } catch (\Throwable $e) {
            Log::error('ImageProcessor compress failed, storing original.', [
                'file' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);

            return $file->store($directory, 'public');
        }
    }

    /**
     * Delete a stored image and its thumbnail (paths relative to public disk).
     */
    public static function delete(?string $imagePath, ?string $thumbnailPath = null): void
    {
        $disk = Storage::disk('public');
        if ($imagePath && $disk->exists($imagePath)) {
            $disk->delete($imagePath);
        }
        if ($thumbnailPath && $thumbnailPath !== $imagePath && $disk->exists($thumbnailPath)) {
            $disk->delete($thumbnailPath);
        }
    }
}
