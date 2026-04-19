# Google Cloud Storage - Quick Reference Guide

## Quick Start

### Enable GCS

In your `.env` file:
```env
GCS_ENABLED=true
GCS_PROJECT_ID=my-gcp-project
GCS_BUCKET=my-storage-bucket
GCS_KEY_FILE=/storage/google-credentials.json
```

### Check if Enabled

```php
$gcs = app(\App\Services\GoogleCloudStorageService::class);
if ($gcs->isEnabled()) {
    echo "GCS is ready!";
}
```

---

## Common Usage Patterns

### 1. Upload Profile Picture (Already Integrated)

**Automatic via uploadImage()**:
```php
// In UserController or any controller
$filename = uploadImage(
    $request->file('profile_picture'),
    'assets/images/user/profile',
    '350x300'  // resize to 350x300
);
```

**Direct via GoogleCloudStorageService**:
```php
$gcs = app(\App\Services\GoogleCloudStorageService::class);

$filename = $gcs->uploadImage(
    $request->file('photo'),
    'assets/images/user/profile',
    '350x300',      // dimensions
    $oldImage,      // old file to delete
    '150x150'       // thumbnail dimensions
);

// Get public URL
$publicUrl = $gcs->getPublicUrl('assets/images/user/profile', $filename);
```

### 2. Upload KYC Documents

**Via KycUploadService**:
```php
$kyc = app(\App\Services\KycUploadService::class);

$result = $kyc->uploadKycDocument(
    $request->file('identity_document'),
    'identity_front',  // document type
    $userId,
    $oldFile  // optional: file to delete
);

if ($result) {
    // Use result
    echo $result['filename'];
    echo $result['url'];
    echo $result['storage'];  // 'gcs' or 'local'
}
```

### 3. Upload Multiple KYC Documents

```php
$kyc = app(\App\Services\KycUploadService::class);

$results = $kyc->uploadMultipleDocuments(
    [
        'identity_front' => $request->file('id_front'),
        'identity_back' => $request->file('id_back'),
        'selfie' => $request->file('selfie'),
        'proof_address' => $request->file('address'),
    ],
    $userId
);

// Check individual results
foreach ($results as $type => $result) {
    if ($result) {
        echo "Uploaded $type: " . $result['filename'];
    } else {
        echo "Failed to upload $type";
    }
}
```

### 4. Generic File Upload

```php
// Upload via helper
$filename = uploadFile(
    $request->file('document'),
    'assets/documents'
);

// Or via service
$gcs = app(\App\Services\GoogleCloudStorageService::class);
$filename = $gcs->uploadFile(
    $request->file('contract'),
    'assets/contracts'
);
```

### 5. Get File URL

```php
$gcs = app(\App\Services\GoogleCloudStorageService::class);

// Get public URL
$url = $gcs->getPublicUrl(
    'assets/images/user/profile',
    'filename.jpg'
);

// With CDN (if configured)
// Returns: https://cdn.example.com/assets/images/user/profile/filename.jpg

// Without CDN
// Returns: https://storage.googleapis.com/bucket-name/assets/images/user/profile/filename.jpg
```

### 6. Delete File

```php
$gcs = app(\App\Services\GoogleCloudStorageService::class);

$success = $gcs->deleteFile(
    'assets/images/user/profile',
    'filename.jpg'
);

// Or via KYC service
$kyc = app(\App\Services\KycUploadService::class);
$success = $kyc->deleteKycDocument(
    'assets/kyc/123',
    'filename.jpg'
);
```

### 7. Check If File Exists

```php
$gcs = app(\App\Services\GoogleCloudStorageService::class);

if ($gcs->fileExists('assets/images/user/profile', 'filename.jpg')) {
    echo "File exists!";
}

// Via KYC service
$kyc = app(\App\Services\KycUploadService::class);
if ($kyc->fileExists('path', 'filename')) {
    // File exists
}
```

### 8. Retrieve File Contents

```php
$gcs = app(\App\Services\GoogleCloudStorageService::class);

$contents = $gcs->getFile(
    'assets/documents',
    'contract.pdf'
);

if ($contents) {
    // Download or process file
    return response()->download($contents);
}
```

### 9. Store File Reference in Database

```php
// Store both filename and storage type
$user->image = $filename;  // Store filename
$user->image_storage = 'gcs';  // Store which storage is used
$user->image_path = 'assets/images/user/profile';  // Store path
$user->save();

// Later, retrieve URL
$url = $gcs->getPublicUrl($user->image_path, $user->image);
```

---

## In Controllers

### Profile Controller Example

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();

        // uploadImage() automatically handles GCS if enabled
        $filename = uploadImage(
            $request->file('photo'),
            'assets/images/user/profile',
            '350x300',
            $user->image  // old file to delete
        );

        if ($filename) {
            $user->image = $filename;
            $user->save();

            return back()->with('success', 'Photo updated successfully!');
        }

        return back()->with('error', 'Failed to upload photo.');
    }
}
```

### KYC Controller Example

```php
<?php

namespace App\Http\Controllers;

use App\Services\KycUploadService;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function __construct(protected KycUploadService $kyc) {}

    public function submit(Request $request)
    {
        $request->validate([
            'identity_front' => 'required|image',
            'identity_back' => 'required|image',
            'selfie' => 'required|image',
        ]);

        $user = auth()->user();

        // Upload all documents
        $results = $this->kyc->uploadMultipleDocuments(
            [
                'identity_front' => $request->file('identity_front'),
                'identity_back' => $request->file('identity_back'),
                'selfie' => $request->file('selfie'),
            ],
            $user->id
        );

        // Check results
        $failed = array_filter($results, fn($r) => is_null($r));
        if ($failed) {
            return back()->with('error', 'Failed to upload some documents.');
        }

        // Store in database
        $user->update([
            'kyc_status' => 'submitted',
            'kyc_submitted_at' => now(),
        ]);

        return back()->with('success', 'KYC documents submitted!');
    }
}
```

---

## Environment Variables

### Required
```env
GCS_ENABLED=true
GCS_PROJECT_ID=my-gcp-project-id
GCS_BUCKET=my-bucket-name
GCS_KEY_FILE=/storage/google-credentials.json
```

### Optional
```env
GCS_CDN_URL=https://cdn.example.com
```

---

## Error Handling

### Try-Catch Pattern

```php
use App\Services\GoogleCloudStorageService;

try {
    $gcs = app(GoogleCloudStorageService::class);
    
    if (!$gcs->isEnabled()) {
        throw new Exception('GCS is not enabled');
    }

    $filename = $gcs->uploadImage(
        $file,
        'assets/images',
        '350x300'
    );

    if (!$filename) {
        throw new Exception('Upload failed');
    }

    return response()->json(['filename' => $filename]);
} catch (Exception $e) {
    \Log::error('Upload error: ' . $e->getMessage());
    return response()->json(['error' => 'Upload failed'], 500);
}
```

### Logging

```php
// Errors are automatically logged in storage/logs/laravel.log

// Check logs
tail -f storage/logs/laravel.log | grep "GCS"

// Search for errors
grep -i "gcs\|upload" storage/logs/laravel.log | tail -20
```

---

## Testing

### Unit Test Example

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;

class GcsUploadTest extends TestCase
{
    public function test_profile_photo_upload()
    {
        $user = $this->actingAsUser();
        
        $file = UploadedFile::fake()->image('profile.jpg', 600, 600);
        
        $response = $this->post('/profile/update-photo', [
            'photo' => $file,
        ]);

        $response->assertRedirect();
        
        // Check user was updated
        $this->assertNotNull($user->fresh()->image);
    }

    public function test_kyc_upload()
    {
        $user = $this->actingAsUser();
        
        $response = $this->post('/kyc/submit', [
            'identity_front' => UploadedFile::fake()->image('id_front.jpg'),
            'identity_back' => UploadedFile::fake()->image('id_back.jpg'),
            'selfie' => UploadedFile::fake()->image('selfie.jpg'),
        ]);

        $response->assertRedirect();
    }
}
```

---

## Troubleshooting Commands

```bash
# Test GCS connection
php artisan tinker
>>> app(\App\Services\GoogleCloudStorageService::class)->isEnabled()

# Check recent errors
tail -f storage/logs/laravel.log

# Search for upload issues
grep -i "upload\|gcs" storage/logs/laravel.log

# Clear Laravel cache
php artisan cache:clear
php artisan view:clear

# Dump service configuration
php artisan tinker
>>> config('services.gcs')
```

---

## Key Points to Remember

✅ **GCS tries first** - If enabled, GCS is tried before local/remote storage
✅ **Automatic fallback** - If GCS fails, local storage is used automatically
✅ **No breaking changes** - All existing upload functions work unchanged
✅ **Logging** - All errors are logged to storage/logs/laravel.log
✅ **CDN support** - Optional CDN URL for faster delivery
✅ **Batch operations** - KycUploadService supports uploading multiple files

---

## Common Mistakes to Avoid

❌ **Don't commit JSON credentials**
- Always add to .gitignore
- Use environment variables

❌ **Don't hardcode paths**
- Use config values
- Use imagePath() function

❌ **Don't ignore error messages**
- Always check logs
- Implement proper error handling

❌ **Don't forget to set permissions**
- Service account needs Storage Admin role
- Bucket needs appropriate ACLs

---

## Links & Resources

- [Google Cloud Console](https://console.cloud.google.com/)
- [GCS Setup Guide](./GCS_SETUP_GUIDE.md)
- [GCS Environment Variables](./GCS_ENV_SETUP.md)
- [Implementation Summary](./GCS_IMPLEMENTATION_SUMMARY.md)
- [Google Cloud Storage Docs](https://cloud.google.com/storage/docs)

---

**Last Updated**: April 19, 2026
**Status**: Ready for Development
