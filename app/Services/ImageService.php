<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image as ImageManager;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;

class ImageService
{
    /**
     * Image size configurations for different contexts.
     * Maintaining good quality while reducing file size.
     */
    public const SIZES = [
        'profile' => [
            'width' => 400,
            'height' => 400,
            'quality' => 85,
        ],
        'product' => [
            'width' => 1200,
            'height' => 1200,
            'quality' => 85,
        ],
        'product_thumbnail' => [
            'width' => 400,
            'height' => 400,
            'quality' => 80,
        ],
        'category' => [
            'width' => 800,
            'height' => 600,
            'quality' => 85,
        ],
    ];

    /**
     * Store an uploaded image and dispatch processing job.
     */
    public function storeAndProcess(
        UploadedFile $file,
        string $directory,
        string $type = 'product'
    ): string {
        $path = $file->store($directory, 'public');
        
        return $path;
    }

    /**
     * Process an image record - resize and optimize.
     */
    public function processImage(Image $image, string $type = 'product'): bool
    {
        try {
            $config = self::SIZES[$type] ?? self::SIZES['product'];
            
            $relativePath = $this->getRelativePath($image->getRawOriginal('url'));
            $fullPath = Storage::disk('public')->path($relativePath);
            
            if (!file_exists($fullPath)) {
                Log::error("Image file not found for processing", [
                    'image_id' => $image->id,
                    'path' => $fullPath
                ]);
                return false;
            }

            $originalSize = filesize($fullPath);
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            
            $img = ImageManager::read($fullPath);
            $originalWidth = $img->width();
            $originalHeight = $img->height();

            $needsResize = $originalWidth > $config['width'] || $originalHeight > $config['height'];
            
            if ($needsResize) {
                $img->scaleDown($config['width'], $config['height']);
            }

            $encoder = $this->getEncoder($extension, $config['quality']);
            $encoded = $img->encode($encoder);
            
            $processedPath = $this->generateProcessedPath($relativePath);
            $processedFullPath = Storage::disk('public')->path($processedPath);
            
            $processedDir = dirname($processedFullPath);
            if (!is_dir($processedDir)) {
                mkdir($processedDir, 0755, true);
            }
            
            $encoded->save($processedFullPath);
            
            $newSize = filesize($processedFullPath);
            $newImg = ImageManager::read($processedFullPath);

            $image->update([
                'original_url' => $image->getRawOriginal('url'),
                'url' => '/storage/' . $processedPath,
                'is_processed' => true,
                'width' => $newImg->width(),
                'height' => $newImg->height(),
                'file_size' => $newSize,
            ]);

            Log::info("Image processed successfully", [
                'image_id' => $image->id,
                'original_size' => $this->formatBytes($originalSize),
                'new_size' => $this->formatBytes($newSize),
                'reduction' => round((1 - $newSize / $originalSize) * 100, 1) . '%',
                'original_dimensions' => "{$originalWidth}x{$originalHeight}",
                'new_dimensions' => "{$newImg->width()}x{$newImg->height()}",
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to process image", [
                'image_id' => $image->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Determine image type based on the imageable model.
     */
    public function getImageType(Image $image): string
    {
        return match ($image->imageable_type) {
            'App\Models\User' => 'profile',
            'App\Models\Product' => 'product',
            'App\Models\Category' => 'category',
            default => 'product',
        };
    }

    /**
     * Get the appropriate encoder based on file extension.
     */
    private function getEncoder(string $extension, int $quality): JpegEncoder|PngEncoder|WebpEncoder
    {
        return match ($extension) {
            'png' => new PngEncoder(),
            'webp' => new WebpEncoder($quality),
            default => new JpegEncoder($quality),
        };
    }

    /**
     * Generate a processed image path.
     */
    private function generateProcessedPath(string $originalPath): string
    {
        $pathInfo = pathinfo($originalPath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];
        
        return "{$directory}/{$filename}_optimized.{$extension}";
    }

    /**
     * Get relative path from URL.
     */
    private function getRelativePath(string $url): string
    {
        return ltrim(str_replace('/storage/', '', $url), '/');
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Delete an image and its files.
     */
    public function deleteImage(Image $image): bool
    {
        try {
            $url = $image->getRawOriginal('url');
            $originalUrl = $image->original_url;
            
            if ($url) {
                $path = $this->getRelativePath($url);
                Storage::disk('public')->delete($path);
            }
            
            if ($originalUrl && $originalUrl !== $url) {
                $originalPath = $this->getRelativePath($originalUrl);
                Storage::disk('public')->delete($originalPath);
            }
            
            $image->delete();
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to delete image", [
                'image_id' => $image->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
