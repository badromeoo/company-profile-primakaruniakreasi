<?php

namespace App\Support;

use GdImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ImageOptimizer
{
    private const QUALITY = 82;

    /**
     * Maximum number of pixels accepted before resizing, to keep GD memory usage bounded.
     */
    private const MAX_PIXELS = 30_000_000;

    /**
     * Convert an image on the given disk to WebP and resize it to fit the maximum dimension.
     *
     * @return string|null The new path when converted, or null when the file is missing, unsupported, or already optimal.
     */
    public function toWebp(string $disk, string $path, int $maxDimension, int $quality = self::QUALITY): ?string
    {
        $storage = Storage::disk($disk);

        try {
            if ($path === '' || ! $storage->exists($path)) {
                return null;
            }

            $bytes = $storage->get($path);
        } catch (Throwable) {
            return null;
        }

        if (! is_string($bytes) || $bytes === '') {
            return null;
        }

        $size = @getimagesizefromstring($bytes);

        if ($size === false || ($size[0] * $size[1]) > self::MAX_PIXELS) {
            return null;
        }

        $image = @imagecreatefromstring($bytes);

        if (! $image instanceof GdImage) {
            return null;
        }

        try {
            $image = $this->applyExifOrientation($image, $this->localPath($storage, $path));

            $width = imagesx($image);
            $height = imagesy($image);

            if ($width < 1 || $height < 1) {
                return null;
            }

            if (Str::endsWith(Str::lower($path), '.webp') && max($width, $height) <= $maxDimension) {
                return null;
            }

            $image = $this->resizeToFit($image, $maxDimension);

            $encoded = $this->encodeWebp($image, $quality);

            if ($encoded === null) {
                return null;
            }

            $newPath = $this->webpPath($path);

            $storage->put($newPath, $encoded, ['visibility' => 'public']);

            if ($newPath !== $path) {
                $storage->delete($path);
            }

            return $newPath;
        } finally {
            imagedestroy($image);
        }
    }

    private function localPath(object $storage, string $path): ?string
    {
        if (! method_exists($storage, 'path')) {
            return null;
        }

        try {
            return $storage->path($path);
        } catch (Throwable) {
            return null;
        }
    }

    private function applyExifOrientation(GdImage $image, ?string $localPath): GdImage
    {
        if ($localPath === null || ! function_exists('exif_read_data') || ! is_readable($localPath)) {
            return $image;
        }

        $exif = @exif_read_data($localPath);
        $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 0) : 0;

        if (! in_array($orientation, [2, 3, 4, 5, 6, 7, 8], true)) {
            return $image;
        }

        $angle = match ($orientation) {
            3, 4 => 180,
            5, 6 => -90,
            7, 8 => 90,
            default => 0,
        };

        if ($angle !== 0) {
            $rotated = imagerotate($image, $angle, 0);

            if ($rotated instanceof GdImage) {
                imagedestroy($image);
                $image = $rotated;
            }
        }

        if (in_array($orientation, [2, 4, 5, 7], true)) {
            imageflip($image, IMG_FLIP_HORIZONTAL);
        }

        return $image;
    }

    private function resizeToFit(GdImage $image, int $maxDimension): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, $maxDimension / max($width, $height));

        if ($scale >= 1) {
            return $image;
        }

        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));
        $resized = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagefilledrectangle(
            $resized,
            0,
            0,
            $newWidth,
            $newHeight,
            imagecolorallocatealpha($resized, 0, 0, 0, 127),
        );

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagedestroy($image);

        return $resized;
    }

    private function encodeWebp(GdImage $image, int $quality): ?string
    {
        ob_start();
        $encoded = imagewebp($image, null, max(0, min(100, $quality)));
        $contents = ob_get_clean();

        if (! $encoded || ! is_string($contents) || $contents === '') {
            return null;
        }

        return $contents;
    }

    private function webpPath(string $path): string
    {
        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $prefix = ($directory === '.' || $directory === '') ? '' : $directory.'/';

        return $prefix.$filename.'.webp';
    }
}
