<?php

namespace App\Services;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Google Cloud Storage Service
 * 
 * Handles all file uploads to Google Cloud Storage bucket
 * Used for profile pictures, KYC images, and other user uploads
 */
class GoogleCloudStorageService
{
    private $storageClient;
    private $bucket;
    private $projectId;
    private $bucketName;
    private $cdnUrl;
    private $enabled;

    public function __construct()
    {
        $this->projectId = config('services.gcs.project_id');
        $this->bucketName = config('services.gcs.bucket');
        $this->cdnUrl = config('services.gcs.cdn_url');
        $this->enabled = config('services.gcs.enabled', false);

        if ($this->enabled && $this->projectId && $this->bucketName) {
            try {
                $credentialsPath = config('services.gcs.key_file');
                
                if ($credentialsPath && file_exists($credentialsPath)) {
                    $this->storageClient = new StorageClient([
                        'projectId' => $this->projectId,
                        'keyFilePath' => $credentialsPath,
                    ]);
                    $this->bucket = $this->storageClient->bucket($this->bucketName);
                } else {
                    Log::warning('GCS key file not found: ' . $credentialsPath);
                    $this->enabled = false;
                }
            } catch (Exception $e) {
                Log::error('Failed to initialize Google Cloud Storage: ' . $e->getMessage());
                $this->enabled = false;
            }
        }
    }

    /**
     * Check if GCS is enabled and properly configured
     */
    public function isEnabled(): bool
    {
        return $this->enabled && $this->bucket !== null;
    }

    /**
     * Upload file to Google Cloud Storage
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path Directory path in bucket
     * @param string|null $oldFile File to delete
     * @return string|null Filename on success, null on failure
     */
    public function uploadFile($file, string $path, ?string $oldFile = null): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            // Generate unique filename
            $filename = $this->generateFilename($file);
            $fullPath = trim($path, '/') . '/' . $filename;

            // Delete old file if provided
            if ($oldFile) {
                $this->deleteFile($path, $oldFile);
            }

            // Read file content
            $contents = file_get_contents($file->getRealPath());

            // Upload to GCS
            $this->bucket->upload(
                $contents,
                [
                    'name' => $fullPath,
                    'metadata' => [
                        'contentType' => $file->getMimeType(),
                        'cacheControl' => 'public, max-age=86400',
                    ],
                ]
            );

            Log::info("File uploaded to GCS: {$fullPath}");

            return $filename;
        } catch (Exception $e) {
            Log::error("GCS upload failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Upload image with resizing to Google Cloud Storage
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path Directory path
     * @param string|null $dimensions Image dimensions (e.g., "350x300")
     * @param string|null $oldFile File to delete
     * @param string|null $thumbDimensions Thumbnail dimensions (e.g., "150x150")
     * @return string|null Filename on success
     */
    public function uploadImage($file, string $path, ?string $dimensions = null, ?string $oldFile = null, ?string $thumbDimensions = null): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $filename = $this->generateFilename($file);
            $fullPath = trim($path, '/') . '/' . $filename;

            // Delete old files if provided
            if ($oldFile) {
                $this->deleteFile($path, $oldFile);
                if ($thumbDimensions) {
                    $this->deleteFile($path, 'thumb_' . $oldFile);
                }
            }

            // Process and upload main image
            $image = \Image::make($file);

            if ($dimensions) {
                $size = explode('x', strtolower($dimensions));
                $image->resize($size[0], $size[1]);
            }

            // Upload main image
            $this->bucket->upload(
                (string) $image->stream($file->getClientOriginalExtension(), 90),
                [
                    'name' => $fullPath,
                    'metadata' => [
                        'contentType' => $file->getMimeType(),
                        'cacheControl' => 'public, max-age=604800',
                    ],
                ]
            );

            // Upload thumbnail if dimensions provided
            if ($thumbDimensions) {
                $thumbSize = explode('x', $thumbDimensions);
                $thumb = \Image::make($file)->resize($thumbSize[0], $thumbSize[1]);
                
                $this->bucket->upload(
                    (string) $thumb->stream($file->getClientOriginalExtension(), 90),
                    [
                        'name' => trim($path, '/') . '/thumb_' . $filename,
                        'metadata' => [
                            'contentType' => $file->getMimeType(),
                            'cacheControl' => 'public, max-age=604800',
                        ],
                    ]
                );
            }

            Log::info("Image uploaded to GCS: {$fullPath}");

            return $filename;
        } catch (Exception $e) {
            Log::error("GCS image upload failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete file from Google Cloud Storage
     * 
     * @param string $path Directory path
     * @param string $filename Filename to delete
     * @return bool Success status
     */
    public function deleteFile(string $path, string $filename): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $fullPath = trim($path, '/') . '/' . $filename;
            $object = $this->bucket->object($fullPath);

            if ($object->exists()) {
                $object->delete();
                Log::info("File deleted from GCS: {$fullPath}");
                return true;
            }

            return false;
        } catch (Exception $e) {
            Log::error("GCS delete failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get public URL for a file in GCS
     * 
     * @param string $path Directory path
     * @param string $filename Filename
     * @return string|null Public URL or null
     */
    public function getPublicUrl(string $path, string $filename): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        if ($this->cdnUrl) {
            return rtrim($this->cdnUrl, '/') . '/' . trim($path, '/') . '/' . $filename;
        }

        // Fallback to GCS public URL
        return "https://storage.googleapis.com/{$this->bucketName}/" . trim($path, '/') . '/' . $filename;
    }

    /**
     * Generate unique filename with timestamp and random ID
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return string Unique filename
     */
    private function generateFilename($file): string
    {
        $extension = $file->getClientOriginalExtension();
        return uniqid() . time() . '.' . $extension;
    }

    /**
     * Get file from GCS (returns stream)
     * 
     * @param string $path Directory path
     * @param string $filename Filename
     * @return string|null File contents or null
     */
    public function getFile(string $path, string $filename): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $fullPath = trim($path, '/') . '/' . $filename;
            $object = $this->bucket->object($fullPath);

            if ($object->exists()) {
                return $object->downloadAsString();
            }

            return null;
        } catch (Exception $e) {
            Log::error("GCS file retrieval failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if file exists in GCS
     * 
     * @param string $path Directory path
     * @param string $filename Filename
     * @return bool File exists status
     */
    public function fileExists(string $path, string $filename): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $fullPath = trim($path, '/') . '/' . $filename;
            return $this->bucket->object($fullPath)->exists();
        } catch (Exception $e) {
            Log::error("GCS exists check failed: " . $e->getMessage());
            return false;
        }
    }
}
