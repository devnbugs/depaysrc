# Google Cloud Storage Environment Variables

## Add these variables to your .env file

### Basic Configuration
```env
# Enable or disable Google Cloud Storage
GCS_ENABLED=false  # Change to true when you have credentials set up

# Google Cloud Project ID
GCS_PROJECT_ID=your-gcp-project-id

# Cloud Storage Bucket Name
GCS_BUCKET=your-bucket-name

# Path to Google Cloud Service Account JSON Key File
# Recommended location: storage/google-credentials.json
# IMPORTANT: Add this file to .gitignore!
GCS_KEY_FILE=/path/to/storage/google-credentials.json

# (Optional) CDN URL for serving files
# Leave empty to use direct Google Cloud Storage URLs
# If using Cloud CDN: https://your-cdn-domain.com
GCS_CDN_URL=
```

## Setup Instructions

### 1. Generate JSON Key File
1. Follow the GCS_SETUP_GUIDE.md for complete instructions
2. Download JSON key from Google Cloud Console
3. Place in: `storage/google-credentials.json`
4. Set GCS_KEY_FILE=/storage/google-credentials.json

### 2. Update .gitignore
Add this line to .gitignore to prevent committing credentials:
```
/storage/google-credentials.json
```

### 3. Enable GCS
Once credentials are in place:
```env
GCS_ENABLED=true
```

### 4. Verify Configuration
Run this command to test:
```bash
php artisan tinker
>>> app(\App\Services\GoogleCloudStorageService::class)->isEnabled()
# Should return true
```

## File Paths After Upload

When GCS is enabled, files are stored in the bucket like:

```
your-bucket-name/
├── assets/images/user/profile/
│   └── {timestamp}.jpg
├── assets/images/verify/withdraw/{year}/{month}/{day}/
│   └── {timestamp}.jpg
├── assets/images/verify/deposit/{year}/{month}/{day}/
│   └── {timestamp}.jpg
└── assets/kyc/{user_id}/{document_type}/{year}/{month}/{day}/
    └── {timestamp}.jpg
```

## Access URLs

### With CDN (Recommended)
```
https://your-cdn-domain.com/assets/images/user/profile/{filename}
```

### Without CDN (Direct Google Cloud Storage)
```
https://storage.googleapis.com/your-bucket-name/assets/images/user/profile/{filename}
```

## Testing Upload

To manually test uploads:

```bash
php artisan tinker

# Create test file
$file = new \Illuminate\Http\UploadedFile(
    storage_path('test.jpg'),
    'test.jpg',
    'image/jpeg',
    null,
    true
);

# Test GCS service
$gcs = app(\App\Services\GoogleCloudStorageService::class);
$result = $gcs->uploadImage($file, 'assets/test');
dd($result);
```

## Troubleshooting

### GCS returns false for isEnabled()
Check:
- GCS_ENABLED is set to true
- GCS_PROJECT_ID is correct
- GCS_BUCKET is correct
- GCS_KEY_FILE points to valid JSON file
- JSON file has correct permissions

### Upload fails silently
Check logs:
```bash
tail -f storage/logs/laravel.log
```

Look for error messages starting with "GCS upload failed" or "Failed to initialize Google Cloud Storage"

### File not accessible after upload
Check:
- Bucket visibility settings
- Service account has Storage Admin permissions
- GCS_CDN_URL is correct (if using CDN)
- Network connectivity

## Switching Storage Backends

The application intelligently falls back through storage options:
1. Google Cloud Storage (if enabled and configured)
2. Remote disk (Cloudflare R2, etc.)
3. Local storage (as final fallback)

To disable GCS and use local storage:
```env
GCS_ENABLED=false
```

## Performance Tips

1. **Use CDN**: Set GCS_CDN_URL to reduce latency
2. **Image Optimization**: Images are auto-resized before upload
3. **Caching**: Files cached with 7-day max-age header
4. **Concurrent Uploads**: GCS handles multiple uploads efficiently

## Security Best Practices

1. ✅ Never commit JSON credentials to version control
2. ✅ Keep JSON key file outside web root
3. ✅ Restrict service account permissions to Storage Admin only
4. ✅ Use environment variables for sensitive data
5. ✅ Enable bucket versioning for backup
6. ✅ Use signed URLs for sensitive files (future enhancement)

## Cost Estimation

Estimated monthly costs for typical VTU/fintech app:

- **Storage**: 100GB = ~$2 USD
- **Egress**: 10GB = ~$1 USD
- **Operations**: Minimal (negligible)

Using Cloud CDN can reduce egress costs by 50-80% for frequently accessed files.

## Support Resources

- [Google Cloud Storage Documentation](https://cloud.google.com/storage/docs)
- [Google Cloud CLI Setup](https://cloud.google.com/sdk/docs/install)
- [Pricing Calculator](https://cloud.google.com/products/calculator)
- [Architecture Best Practices](https://cloud.google.com/storage/docs/best-practices)
