<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
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

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $extension = 'jpg';
        }

        $name = Str::random(24).'.'.$extension;
        $imagePath = $directory.'/'.$name;
        $thumbPath = $directory.'/thumbnails/'.$name;

        try {
            $main = $manager->read($file->getRealPath());
            $main->scaleDown(width: $maxWidth);
            $mainEncoded = $main->encode(new AutoEncoder(quality: $quality));
            Storage::disk('public')->put($imagePath, (string) $mainEncoded);

            $thumb = $manager->read($file->getRealPath());
            $thumb->scaleDown(width: $thumbWidth);
            $thumbEncoded = $thumb->encode(new AutoEncoder(quality: $quality));
            Storage::disk('public')->put($thumbPath, (string) $thumbEncoded);

            return [
                'image' => $imagePath,
                'thumbnail' => $thumbPath,
            ];
        } catch (\Throwable $e) {
            Log::warning('ImageProcessor failed, falling back to direct store.', [
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
