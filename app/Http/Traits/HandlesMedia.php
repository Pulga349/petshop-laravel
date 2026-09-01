<?php
namespace App\Http\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesMedia
{
    /**
     * Handle image upload and return the stored file path.
     *
     * @param UploadedFile $file The uploaded file
     * @param string $directory The storage directory (e.g., 'products', 'suppliers')
     * @return string|null The stored file path or null if upload fails
     */
    public function handleImageUpload(UploadedFile $file, string $directory): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        return $file->store($directory, 'public');
    }

    /**
     * Handle image deletion from storage.
     *
     * @param string|null $path The file path to delete
     * @return bool True if deletion succeeded or file didn't exist
     */
    public function handleImageDelete(?string $path): bool
    {
        if (empty($path)) {
            return true;
        }

        return Storage::disk('public')->delete($path);
    }

    /**
     * Replace existing image with a new one.
     *
     * @param UploadedFile|null $newFile The new uploaded file
     * @param string|null $oldPath The old file path to replace
     * @param string $directory The storage directory
     * @return string|null The new file path or null if no new file uploaded
     */
    public function handleImageReplace(?UploadedFile $newFile, ?string $oldPath, string $directory): ?string
    {
        if ($newFile && $newFile->isValid()) {
            // Delete old image first
            $this->handleImageDelete($oldPath);
            // Upload new image
            return $this->handleImageUpload($newFile, $directory);
        }

        return $oldPath;
    }
}