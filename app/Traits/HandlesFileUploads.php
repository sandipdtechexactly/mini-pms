<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Trait HandlesFileUploads
 * 
 * Provides functionality for handling file uploads in models.
 */
trait HandlesFileUploads
{
    /**
     * Upload a file to the specified disk.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $disk
     * @param string|null $filename
     * @return string|null
     */
    public function uploadFile(
        UploadedFile $file, 
        string $directory = 'uploads', 
        string $disk = 'public',
        ?string $filename = null
    ): ?string {
        if (!$file->isValid()) {
            return null;
        }

        $extension = $file->getClientOriginalExtension();
        $filename = $filename ?: Str::random(40) . '.' . $extension;
        
        $path = $file->storeAs(
            $directory,
            $filename,
            ['disk' => $disk]
        );

        return $path ?: null;
    }

    /**
     * Delete a file from storage.
     *
     * @param string|null $path
     * @param string $disk
     * @return bool
     */
    public function deleteFile(?string $path, string $disk = 'public'): bool
    {
        if (empty($path)) {
            return false;
        }

        return Storage::disk($disk)->delete($path);
    }

    /**
     * Get the URL for a stored file.
     *
     * @param string|null $path
     * @param string $disk
     * @return string|null
     */
    public function getFileUrl(?string $path, string $disk = 'public'): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * Generate a unique filename with timestamp.
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = Str::slug($filename) . '-' . time() . '.' . $extension;
        
        return $filename;
    }
}
