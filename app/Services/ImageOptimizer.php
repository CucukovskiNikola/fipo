<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ImageOptimizer
{
    // Optimization profiles
    public const PROFILES = [
        'thumbnail' => ['width' => 400, 'height' => 300, 'quality' => 80],
        'medium' => ['width' => 800, 'height' => 600, 'quality' => 85],
        'large' => ['width' => 1200, 'height' => 800, 'quality' => 85],
        'full' => ['width' => 1920, 'height' => 1080, 'quality' => 90],
    ];

    /**
     * Optimize uploaded image
     */
    public static function optimizeImage(UploadedFile $file, string $profile = 'large'): string
    {
        // Check if GD extension is available first
        if (!extension_loaded('gd')) {
            Log::warning('GD extension not available, storing original file');
            return self::storeOriginalFile($file);
        }

        try {
            $config = self::PROFILES[$profile] ?? self::PROFILES['large'];
            return self::processImage($file, $config['width'], $config['height'], $config['quality']);
        } catch (\Exception $e) {
            Log::error('Image optimization failed: ' . $e->getMessage());
            return self::storeOriginalFile($file);
        }
    }

    /**
     * Optimize uploaded image with custom settings
     */
    public static function optimizeImageCustom(UploadedFile $file, int $maxWidth = 1200, int $maxHeight = 800, int $quality = 85): string
    {
        // Check if GD extension is available first
        if (!extension_loaded('gd')) {
            Log::warning('GD extension not available, storing original file');
            return self::storeOriginalFile($file);
        }

        try {
            return self::processImage($file, $maxWidth, $maxHeight, $quality);
        } catch (\Exception $e) {
            Log::error('Image optimization failed: ' . $e->getMessage());
            return self::storeOriginalFile($file);
        }
    }

    /**
     * Process the image optimization
     */
    private static function processImage(UploadedFile $file, int $maxWidth, int $maxHeight, int $quality): string
    {
        $originalPath = $file->getPathname();
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        Log::info('Optimizing image', [
            'original_size' => $file->getSize(),
            'mime_type' => $mimeType,
            'extension' => $extension
        ]);


        // Load image based on type
        $sourceImage = null;
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $sourceImage = \imagecreatefromjpeg($originalPath);
                break;
            case 'image/png':
                $sourceImage = \imagecreatefrompng($originalPath);
                break;
            case 'image/gif':
                $sourceImage = \imagecreatefromgif($originalPath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $sourceImage = \imagecreatefromwebp($originalPath);
                } else {
                    Log::warning('WebP support not available, storing original');
                    return self::storeOriginalFile($file);
                }
                break;
            default:
                // Unsupported format, return original
                return self::storeOriginalFile($file);
        }

        if (!$sourceImage) {
            Log::warning('Failed to create image resource, storing original');
            return self::storeOriginalFile($file);
        }

        // Get original dimensions
        $originalWidth = \imagesx($sourceImage);
        $originalHeight = \imagesy($sourceImage);

        // Calculate new dimensions while maintaining aspect ratio
        [$newWidth, $newHeight] = self::calculateDimensions($originalWidth, $originalHeight, $maxWidth, $maxHeight);

        // Only resize if the image is larger than max dimensions
        if ($newWidth < $originalWidth || $newHeight < $originalHeight) {
            // Create new image with calculated dimensions
            $optimizedImage = \imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and GIF
            if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                \imagealphablending($optimizedImage, false);
                \imagesavealpha($optimizedImage, true);
                $transparent = \imagecolorallocatealpha($optimizedImage, 255, 255, 255, 127);
                \imagefilledrectangle($optimizedImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resize image
            \imagecopyresampled(
                $optimizedImage,
                $sourceImage,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $originalWidth, $originalHeight
            );
        } else {
            $optimizedImage = $sourceImage;
        }

        // Generate optimized filename
        $filename = self::generateFilename($file, $extension);
        $storagePath = storage_path('app/public/partners/' . $filename);

        // Ensure directory exists
        $directory = dirname($storagePath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save optimized image
        $saved = false;
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $saved = \imagejpeg($optimizedImage, $storagePath, $quality);
                break;
            case 'image/png':
                // PNG quality is 0-9 (0 = no compression, 9 = max compression)
                $pngQuality = (int) ((100 - $quality) / 11.11);
                $saved = \imagepng($optimizedImage, $storagePath, $pngQuality);
                break;
            case 'image/gif':
                $saved = \imagegif($optimizedImage, $storagePath);
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    $saved = \imagewebp($optimizedImage, $storagePath, $quality);
                } else {
                    Log::warning('WebP save not supported');
                    $saved = false;
                }
                break;
        }

        // Clean up memory
        \imagedestroy($sourceImage);
        if ($optimizedImage !== $sourceImage) {
            \imagedestroy($optimizedImage);
        }

        if ($saved) {
            $optimizedSize = filesize($storagePath);
            $compressionRatio = round((1 - ($optimizedSize / $file->getSize())) * 100, 2);
            
            Log::info('Image optimized successfully', [
                'original_size' => $file->getSize(),
                'optimized_size' => $optimizedSize,
                'compression_ratio' => $compressionRatio . '%',
                'dimensions' => "{$newWidth}x{$newHeight}"
            ]);

            return 'partners/' . $filename;
        }

        Log::warning('Failed to save optimized image, storing original');
        return self::storeOriginalFile($file);
    }

    /**
     * Calculate new dimensions while maintaining aspect ratio
     */
    private static function calculateDimensions(int $originalWidth, int $originalHeight, int $maxWidth, int $maxHeight): array
    {
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        
        if ($ratio >= 1) {
            // Image is smaller than max dimensions, keep original size
            return [$originalWidth, $originalHeight];
        }

        $newWidth = (int) round($originalWidth * $ratio);
        $newHeight = (int) round($originalHeight * $ratio);

        return [$newWidth, $newHeight];
    }

    /**
     * Generate unique filename
     */
    private static function generateFilename(UploadedFile $file, string $extension): string
    {
        $hash = md5(uniqid() . $file->getClientOriginalName() . time());
        return $hash . '.' . $extension;
    }

    /**
     * Store original file without optimization
     */
    private static function storeOriginalFile(UploadedFile $file): string
    {
        return $file->store('partners', 'public');
    }
}