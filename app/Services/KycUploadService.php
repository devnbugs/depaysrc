<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

/**
 * KYC Upload Service
 * 
 * Handles KYC document uploads including:
 * - Identity verification documents
 * - Proof of address
 * - Selfie images
 * - Bank statements
 * - Business licenses
 */
class KycUploadService
{
    protected GoogleCloudStorageService $gcsService;

    public function __construct(GoogleCloudStorageService $gcsService)
    {
        $this->gcsService = $gcsService;
    }

    /**
     * Upload KYC document
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $documentType Type of document (identity, address, selfie, etc.)
     * @param int $userId User ID
     * @param string|null $oldFile Previous file to delete
     * @return array|null Array with filename and path, or null on failure
     */
    public function uploadKycDocument($file, string $documentType, int $userId, ?string $oldFile = null): ?array
    {
        try {
            // Generate secure path
            $path = $this->getKycPath($userId, $documentType);

            // Delete old file if provided
            if ($oldFile) {
                $this->deleteKycDocument($path, $oldFile);
            }

            // Upload to GCS if available
            if ($this->gcsService->isEnabled()) {
                $filename = $this->gcsService->uploadFile($file, $path, $oldFile);
                if ($filename) {
                    return [
                        'filename' => $filename,
                        'path' => $path,
                        'document_type' => $documentType,
                        'storage' => 'gcs',
                        'url' => $this->gcsService->getPublicUrl($path, $filename),
                    ];
                }
            }

            // Fallback to local/remote storage
            $filename = $this->uploadToLocalStorage($file, $path, $oldFile);
            if ($filename) {
                return [
                    'filename' => $filename,
                    'path' => $path,
                    'document_type' => $documentType,
                    'storage' => 'local',
                ];
            }

            return null;
        } catch (Exception $e) {
            Log::error("KYC document upload failed: " . $e->getMessage(), [
                'user_id' => $userId,
                'document_type' => $documentType,
                'file_name' => $file->getClientOriginalName(),
            ]);
            return null;
        }
    }

    /**
     * Upload KYC image with resize (for selfies, ID photos)
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $documentType Type of image
     * @param int $userId User ID
     * @param string|null $oldFile Previous file to delete
     * @param string $dimensions Image dimensions (e.g., "600x800")
     * @return array|null Array with filename and metadata
     */
    public function uploadKycImage($file, string $documentType, int $userId, ?string $oldFile = null, string $dimensions = "600x800"): ?array
    {
        try {
            $path = $this->getKycPath($userId, $documentType);

            // Upload to GCS if available
            if ($this->gcsService->isEnabled()) {
                $filename = $this->gcsService->uploadImage($file, $path, $dimensions, $oldFile);
                if ($filename) {
                    return [
                        'filename' => $filename,
                        'path' => $path,
                        'document_type' => $documentType,
                        'storage' => 'gcs',
                        'url' => $this->gcsService->getPublicUrl($path, $filename),
                    ];
                }
            }

            // Fallback to local/remote storage
            $filename = uploadImage($file, $path, $dimensions, $oldFile);
            if ($filename) {
                return [
                    'filename' => $filename,
                    'path' => $path,
                    'document_type' => $documentType,
                    'storage' => 'local',
                ];
            }

            return null;
        } catch (Exception $e) {
            Log::error("KYC image upload failed: " . $e->getMessage(), [
                'user_id' => $userId,
                'document_type' => $documentType,
                'file_name' => $file->getClientOriginalName(),
            ]);
            return null;
        }
    }

    /**
     * Upload multiple KYC documents
     * 
     * @param array $files Key-value array of document type => file
     * @param int $userId User ID
     * @param array $oldFiles Previous files to delete
     * @return array Results array with success/failure for each document
     */
    public function uploadMultipleDocuments(array $files, int $userId, array $oldFiles = []): array
    {
        $results = [];

        foreach ($files as $documentType => $file) {
            if (!$file) {
                continue;
            }

            $oldFile = $oldFiles[$documentType] ?? null;

            if ($this->isImageDocument($documentType)) {
                $result = $this->uploadKycImage($file, $documentType, $userId, $oldFile);
            } else {
                $result = $this->uploadKycDocument($file, $documentType, $userId, $oldFile);
            }

            $results[$documentType] = $result;
        }

        return $results;
    }

    /**
     * Delete KYC document
     * 
     * @param string $path Document path
     * @param string $filename Filename to delete
     * @return bool Success status
     */
    public function deleteKycDocument(string $path, string $filename): bool
    {
        // Try GCS first
        if ($this->gcsService->isEnabled()) {
            if ($this->gcsService->deleteFile($path, $filename)) {
                return true;
            }
        }

        // Fallback to local
        return \removeFile($path . '/' . $filename);
    }

    /**
     * Get KYC storage path
     * 
     * @param int $userId User ID
     * @param string $documentType Type of document
     * @return string Storage path
     */
    protected function getKycPath(int $userId, string $documentType): string
    {
        $date = date("Y/m/d");
        return "assets/kyc/{$userId}/{$documentType}/{$date}";
    }

    /**
     * Check if document is an image that should be resized
     * 
     * @param string $documentType Document type
     * @return bool Is image
     */
    protected function isImageDocument(string $documentType): bool
    {
        $imageDocuments = [
            'identity_front',
            'identity_back',
            'selfie',
            'proof_of_address',
            'profile_picture',
        ];

        return in_array($documentType, $imageDocuments);
    }

    /**
     * Fallback upload to local storage
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path Storage path
     * @param string|null $oldFile File to delete
     * @return string|null Filename on success
     */
    protected function uploadToLocalStorage($file, string $path, ?string $oldFile = null): ?string
    {
        try {
            return uploadFile($file, $path, null, $oldFile);
        } catch (Exception $e) {
            Log::error("Local KYC upload failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get public URL for KYC document
     * 
     * @param string $path Document path
     * @param string $filename Filename
     * @return string|null Public URL
     */
    public function getPublicUrl(string $path, string $filename): ?string
    {
        if ($this->gcsService->isEnabled()) {
            return $this->gcsService->getPublicUrl($path, $filename);
        }

        // Fallback to local URL
        return asset('storage/' . trim($path, '/') . '/' . $filename);
    }

    /**
     * Get KYC file (retrieve from storage)
     * 
     * @param string $path File path
     * @param string $filename Filename
     * @return string|null File contents or null
     */
    public function getFile(string $path, string $filename): ?string
    {
        if ($this->gcsService->isEnabled()) {
            return $this->gcsService->getFile($path, $filename);
        }

        // Fallback to local file
        try {
            $fullPath = public_path('storage/' . trim($path, '/') . '/' . $filename);
            if (file_exists($fullPath)) {
                return file_get_contents($fullPath);
            }
        } catch (Exception $e) {
            Log::error("Local file retrieval failed: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Check if KYC file exists
     * 
     * @param string $path File path
     * @param string $filename Filename
     * @return bool File exists
     */
    public function fileExists(string $path, string $filename): bool
    {
        if ($this->gcsService->isEnabled()) {
            return $this->gcsService->fileExists($path, $filename);
        }

        // Fallback to local check
        return file_exists(public_path('storage/' . trim($path, '/') . '/' . $filename));
    }
}
