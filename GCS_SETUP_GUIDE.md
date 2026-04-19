# Google Cloud Storage Setup Guide

## Overview
This document provides instructions for setting up Google Cloud Storage (GCS) for storing user-uploaded files including:
- User profile pictures
- KYC verification documents (withdraw & deposit)
- Identity verification images
- Other user-related uploads

## Prerequisites

1. **Google Cloud Project**: Create a project in Google Cloud Console
2. **Service Account**: Create a service account with Storage Admin permissions
3. **Storage Bucket**: Create a Cloud Storage bucket in your project
4. **JSON Key File**: Download the service account key as JSON

## Step-by-Step Setup

### 1. Create Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project
3. Note your **PROJECT_ID**

### 2. Enable Cloud Storage API
1. Navigate to APIs & Services > Library
2. Search for "Cloud Storage API"
3. Click "Enable"

### 3. Create Service Account
1. Go to APIs & Services > Service Accounts
2. Click "Create Service Account"
3. Fill in the service account details
4. Click "Create and Continue"
5. Add the "Storage Admin" role
6. Click "Continue" and "Done"

### 4. Create JSON Key
1. Click on the created service account
2. Go to "Keys" tab
3. Click "Add Key" > "Create new key"
4. Choose "JSON"
5. Click "Create" - this downloads the JSON file
6. Save this file securely in your project (e.g., `storage/google-credentials.json`)

### 5. Create Storage Bucket
1. Go to Cloud Storage > Buckets
2. Click "Create Bucket"
3. Choose a bucket name (e.g., `your-app-uploads`)
4. Choose storage class: "Standard" (recommended)
5. Choose location close to your users
6. Click "Create"
7. Note your **BUCKET_NAME**

### 6. Configure Bucket Permissions
1. Open the bucket settings
2. Go to Permissions tab
3. Add your service account email with "Storage Object Admin" role
4. Apply changes

### 7. (Optional) Setup CDN
For faster delivery of user uploads, set up a CDN:
1. Go to Cloud CDN settings
2. Configure your bucket as a backend
3. Note your **CDN_URL** (e.g., `https://cdn.your-domain.com`)

## Environment Variables

Add the following to your `.env` file:

```env
# Google Cloud Storage Configuration
GCS_ENABLED=true
GCS_PROJECT_ID=your-gcp-project-id
GCS_BUCKET=your-bucket-name
GCS_KEY_FILE=/path/to/google-credentials.json
GCS_CDN_URL=https://cdn.your-domain.com  # Optional - leave empty if not using CDN
```

## File Locations in Bucket

When GCS is enabled, files will be stored in the following structure:

```
your-bucket-name/
├── assets/images/user/profile/
│   ├── {filename}.jpg        # User profile pictures
│   └── thumb_{filename}.jpg  # Profile picture thumbnails
├── assets/images/verify/withdraw/
│   └── {year}/{month}/{day}/
│       └── {filename}.jpg    # Withdrawal verification documents
└── assets/images/verify/deposit/
    └── {year}/{month}/{day}/
        └── {filename}.jpg    # Deposit verification documents
```

## File Access

### Public URLs
Files are stored with public visibility. Access them using:
- **With CDN**: `https://cdn.your-domain.com/assets/images/user/profile/{filename}`
- **Without CDN**: `https://storage.googleapis.com/your-bucket-name/assets/images/user/profile/{filename}`

The application automatically generates the correct URLs based on your GCS configuration.

## Error Handling

If GCS upload fails:
1. Application falls back to local/remote storage automatically
2. Check logs in `storage/logs/laravel.log`
3. Verify:
   - GCS_ENABLED is set to true
   - JSON credentials file exists at GCS_KEY_FILE path
   - Service account has Storage Admin permissions
   - Bucket exists and is accessible
   - Network connectivity to Google Cloud

## Fallback Mechanism

The application includes intelligent fallback:
1. Tries to upload to GCS first (if enabled)
2. If GCS fails, automatically falls back to local/remote storage
3. If local/remote storage fails, throws exception

This ensures service availability even if GCS is temporarily unavailable.

## Testing

To test GCS integration:

```bash
# Check if GCS is properly configured
php artisan tinker
>>> app(\App\Services\GoogleCloudStorageService::class)->isEnabled()
# Should return true if properly configured
```

## Performance Considerations

- **First Upload**: Images are resized on-the-fly before upload
- **Caching**: Files are cached with max-age=604800 (7 days)
- **CDN Integration**: Use CDN URL for significant performance gains
- **Concurrent Uploads**: GCS handles concurrent uploads efficiently

## Costs

Google Cloud Storage pricing includes:
- Storage costs (per GB/month)
- Network egress charges
- Operations charges (negligible)

Use Cloud CDN to reduce egress costs for frequently accessed files.

## Troubleshooting

### "Invalid project ID"
- Verify GCS_PROJECT_ID matches your Google Cloud project ID
- Check for typos or extra spaces

### "Failed to authenticate"
- Verify JSON key file exists at GCS_KEY_FILE path
- Confirm service account has Storage Admin permissions
- Check file permissions on the JSON key file

### "Bucket not found"
- Verify GCS_BUCKET name is correct (case-sensitive)
- Ensure bucket exists in the correct region
- Check service account has access to the bucket

### "Upload fails silently"
- Check `storage/logs/laravel.log` for error messages
- Verify network connectivity to Google Cloud
- Ensure bucket has sufficient quota

## Switching Between Storage

To disable GCS and use local/remote storage:
```env
GCS_ENABLED=false
```

To re-enable GCS:
```env
GCS_ENABLED=true
```

New uploads will use the configured storage, but existing URLs will remain unchanged.

## Security Notes

1. **Keep JSON Key Secure**: Never commit JSON credentials to version control
2. **Use .gitignore**: Add `google-credentials.json` to `.gitignore`
3. **Restrict Permissions**: Service account should have minimal necessary permissions
4. **Enable Bucket Versioning**: For disaster recovery
5. **Use Signed URLs**: For sensitive files (not currently implemented)

## Support

For issues related to:
- **Google Cloud Setup**: See [Google Cloud Documentation](https://cloud.google.com/docs)
- **Laravel/PHP Integration**: Check application logs in `storage/logs/`
- **File Upload Issues**: Review helper functions in `app/Http/Helpers/helpers.php`
