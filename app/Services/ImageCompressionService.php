<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class ImageCompressionService
{
    /**
     * Convert and compress image to WebP format
     *
     * @return string Path to the compressed image
     */
    public function convertToWebP(UploadedFile $file, array $options = []): string
    {
        $quality = $options['quality'] ?? 80;
        $maxWidth = $options['max_width'] ?? 1920;
        $maxHeight = $options['max_height'] ?? 1080;
        $path = $options['path'] ?? 'images';

        // Create unique filename
        $filename = Str::uuid().'.webp';
        $fullPath = $path.'/'.$filename;

        // Load and process image
        $image = Image::make($file->getRealPath());

        // Resize if necessary (maintaining aspect ratio)
        if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
            $image->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize(); // Prevent upsizing
            });
        }

        // Convert to WebP and compress
        $webpImage = $image->encode('webp', $quality);

        // Store the image
        Storage::disk('public')->put($fullPath, $webpImage);

        // Log compression stats
        \Log::info('Image compressed', [
            'original_size' => $file->getSize(),
            'compressed_size' => strlen($webpImage),
            'compression_ratio' => round((1 - strlen($webpImage) / $file->getSize()) * 100, 2).'%',
            'path' => $fullPath,
        ]);

        return $fullPath;
    }

    /**
     * Process multiple images
     */
    public function processMultipleImages(array $files, array $options = []): array
    {
        $processedPaths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                try {
                    $path = $this->convertToWebP($file, $options);
                    $processedPaths[] = $path;
                } catch (\Exception $e) {
                    \Log::error('Failed to process image', [
                        'file' => $file->getClientOriginalName(),
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $processedPaths;
    }
}
