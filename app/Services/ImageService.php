<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Process, resize, compress and store uploaded homework image.
     * Returns an array with stored relative path, base64 data, and mime type.
     */
    public function processAndStore(UploadedFile $file, string $directory = 'homework'): array
    {
        $image = $this->manager->decodePath($file->getRealPath());

        // Resize down to max 1600px width/height while keeping aspect ratio
        $image->scaleDown(1600, 1600);

        // Compress to JPEG with 80% quality
        $encoded = $image->encodeUsingFileExtension('jpg', 80);
        $binaryData = (string) $encoded;

        // Generate a unique filename
        $filename = Str::uuid() . '.jpg';
        $storagePath = $directory . '/' . $filename;

        // Store to public disk
        Storage::disk('public')->put($storagePath, $binaryData);

        return [
            'path' => $storagePath,
            'url' => Storage::disk('public')->url($storagePath),
            'base64' => base64_encode($binaryData),
            'mime_type' => 'image/jpeg',
            'size' => strlen($binaryData),
        ];
    }
}
