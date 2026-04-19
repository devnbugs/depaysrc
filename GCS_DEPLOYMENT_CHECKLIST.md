# Google Cloud Storage - Deployment Checklist

## 📋 Pre-Deployment Checklist

Before deploying Google Cloud Storage integration, complete these steps:

### Phase 1: Google Cloud Setup

- [ ] Google Cloud account created and ready
- [ ] New GCP project created
- [ ] Project ID noted: `___________________`
- [ ] Billing enabled for the project
- [ ] Cloud Storage API enabled
- [ ] Service account created with descriptive name
- [ ] Service account email noted: `___________________`
- [ ] "Storage Admin" role assigned to service account
- [ ] JSON key file created and downloaded
- [ ] JSON key file stored in `storage/google-credentials.json`
- [ ] JSON key file permissions set correctly
- [ ] Cloud Storage bucket created
- [ ] Bucket name noted: `___________________`
- [ ] Bucket region selected (closest to users)
- [ ] Bucket visibility/ACLs configured
- [ ] (Optional) Cloud CDN configured
- [ ] (Optional) CDN URL noted: `___________________`

### Phase 2: Application Setup

- [ ] Application code updated with GCS integration
- [ ] Composer dependencies updated (`composer install`)
- [ ] New service files created:
  - [ ] `app/Services/GoogleCloudStorageService.php`
  - [ ] `app/Services/KycUploadService.php`
- [ ] Helper functions updated:
  - [ ] `app/Http/Helpers/helpers.php` (uploadImage, uploadFile)
- [ ] Configuration updated:
  - [ ] `config/services.php` (gcs section added)
- [ ] All syntax validated (no PHP errors)

### Phase 3: Environment Configuration

- [ ] `.gitignore` updated to exclude credentials:
  ```
  /storage/google-credentials.json
  ```

- [ ] `.env` file updated with required variables:
  ```env
  GCS_ENABLED=false      # Keep false for testing first
  GCS_PROJECT_ID=your-project-id
  GCS_BUCKET=your-bucket
  GCS_KEY_FILE=/storage/google-credentials.json
  GCS_CDN_URL=           # Optional
  ```

- [ ] Verify JSON key file exists:
  ```bash
  ls -la storage/google-credentials.json
  ```

- [ ] Verify environment variables are set:
  ```bash
  php artisan config:show services.gcs
  ```

### Phase 4: Testing (Staging Environment)

- [ ] Set `GCS_ENABLED=true` in `.env`
- [ ] Test GCS connection:
  ```bash
  php artisan tinker
  >>> app(\App\Services\GoogleCloudStorageService::class)->isEnabled()
  # Should return: true
  ```

- [ ] Create test profile photo upload:
  - [ ] Login to application
  - [ ] Upload profile picture
  - [ ] Verify file appears in GCS bucket
  - [ ] Verify public URL is accessible

- [ ] Create test KYC document upload:
  - [ ] Submit KYC documents
  - [ ] Verify files appear in GCS bucket
  - [ ] Verify files are accessible

- [ ] Test fallback behavior:
  - [ ] Set `GCS_ENABLED=false`
  - [ ] Upload test file
  - [ ] Verify file goes to local storage
  - [ ] Set `GCS_ENABLED=true`
  - [ ] Verify GCS is used again

- [ ] Check error logs:
  ```bash
  tail -f storage/logs/laravel.log | grep -i "gcs\|upload"
  ```

- [ ] Test with real user scenarios:
  - [ ] New user registration + profile photo
  - [ ] KYC verification submission
  - [ ] Withdrawal with documents
  - [ ] Deposit with documents

- [ ] Verify database storage:
  - [ ] User image filename stored in database
  - [ ] KYC document references stored
  - [ ] URLs generate correctly

### Phase 5: Documentation Review

- [ ] Read GCS_SETUP_GUIDE.md - Complete
- [ ] Read GCS_ENV_SETUP.md - Complete
- [ ] Read GCS_QUICK_REFERENCE.md - Complete
- [ ] Read GCS_IMPLEMENTATION_SUMMARY.md - Complete
- [ ] Team members briefed on GCS integration
- [ ] Documentation saved in team wiki/knowledge base

### Phase 6: Performance Testing

- [ ] Monitor upload speeds:
  - [ ] Profile photo: Target < 2 seconds
  - [ ] KYC documents: Target < 5 seconds

- [ ] Test concurrent uploads:
  - [ ] 10 simultaneous profile uploads
  - [ ] No errors or timeouts

- [ ] Verify image resizing:
  - [ ] Original image: > 1MB
  - [ ] Resized image: < 200KB
  - [ ] Thumbnails created correctly

- [ ] Check bucket storage usage:
  - [ ] Verify expected size
  - [ ] No unexpected large files

### Phase 7: Security Review

- [ ] JSON credentials NOT in version control
- [ ] JSON credentials NOT in any commits
- [ ] `.gitignore` properly configured
- [ ] Environment variables used for sensitive data
- [ ] Service account permissions minimal (Storage Admin only)
- [ ] Bucket ACLs reviewed and appropriate
- [ ] Public files don't contain sensitive data
- [ ] Error messages don't expose credentials

### Phase 8: Production Deployment

- [ ] Code merged to main branch
- [ ] All tests passing in CI/CD pipeline
- [ ] Staging environment fully tested
- [ ] Production JSON credentials configured
- [ ] Production `.env` variables set
- [ ] Database backed up before deployment
- [ ] Deployment window scheduled
- [ ] Team notified of deployment
- [ ] Rollback procedure documented
- [ ] Deploy to production servers

### Phase 9: Post-Deployment Verification

- [ ] Application starts without errors
- [ ] Logs show no GCS initialization errors
- [ ] Profile photo uploads work
- [ ] KYC document uploads work
- [ ] URLs are accessible
- [ ] CDN working (if configured)
- [ ] Fallback tested:
  - [ ] Set GCS_ENABLED=false
  - [ ] Upload test file
  - [ ] Verify local storage works
  - [ ] Set GCS_ENABLED=true

### Phase 10: Monitoring & Maintenance

- [ ] Set up log monitoring:
  - [ ] Alert on "GCS upload failed" errors
  - [ ] Alert on authentication failures
  - [ ] Monitor upload success rate

- [ ] Set up bucket monitoring:
  - [ ] Monitor storage growth
  - [ ] Monitor operation costs
  - [ ] Set up budget alerts

- [ ] Weekly checks:
  - [ ] Review GCS logs
  - [ ] Check storage costs
  - [ ] Verify no errors in application logs
  - [ ] Test a few file downloads

- [ ] Monthly reviews:
  - [ ] Cost analysis
  - [ ] Performance metrics
  - [ ] Security audit
  - [ ] Backup verification

---

## 🚨 Rollback Plan

If issues arise and you need to rollback:

### Step 1: Disable GCS Immediately
```env
GCS_ENABLED=false
```

### Step 2: Restart Application
```bash
php artisan config:cache
php artisan cache:clear
```

### Step 3: All New Uploads Use Local Storage
- Existing GCS URLs will still work
- All new uploads automatically use local storage

### Step 4: Monitor Logs
```bash
tail -f storage/logs/laravel.log
```

### Step 5: Complete Rollback (if needed)
```bash
# 1. Revert code changes
git revert <commit-hash>

# 2. Remove GCS package
composer remove google/cloud-storage

# 3. Clear cache
php artisan config:cache
php artisan cache:clear

# 4. Restart application
```

---

## 📞 Support Checklist

When troubleshooting, verify:

- [ ] GCS_ENABLED is set to true in .env
- [ ] GCS_PROJECT_ID matches Google Cloud project ID
- [ ] GCS_BUCKET matches bucket name exactly (case-sensitive)
- [ ] GCS_KEY_FILE points to valid JSON file
- [ ] JSON file has read permissions
- [ ] Service account has Storage Admin role
- [ ] Bucket exists in Google Cloud
- [ ] Network connectivity to Google Cloud (test ping)
- [ ] JSON credentials are not expired
- [ ] Bucket doesn't have object locks enabled
- [ ] CORS configured if serving to browser

---

## 📊 Success Metrics

After deployment, monitor these metrics:

| Metric | Target | Actual |
|--------|--------|--------|
| Upload Success Rate | > 99% | ___ |
| Average Upload Time | < 2s | ___ |
| Image Load Time | < 200ms | ___ |
| Error Rate | < 0.1% | ___ |
| Cost per Upload | < $0.001 | ___ |
| Storage Utilization | Expected | ___ |

---

## 📝 Sign-Off

### Deployed By: _________________ Date: _________

### Verified By: _________________ Date: _________

### Notes:
```
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
```

---

## 🔗 Quick Links

- [Google Cloud Console](https://console.cloud.google.com/)
- [GCS Setup Guide](./GCS_SETUP_GUIDE.md)
- [Quick Reference](./GCS_QUICK_REFERENCE.md)
- [Implementation Summary](./GCS_IMPLEMENTATION_SUMMARY.md)
- [Application Logs](./storage/logs/laravel.log)

---

## ✅ Final Verification

Run this command to verify everything is ready:

```bash
#!/bin/bash

echo "=== GCS Integration Verification ==="
echo ""

# Check config exists
echo "1. Checking config..."
php artisan config:show services.gcs

echo ""
echo "2. Checking environment..."
echo "GCS_ENABLED: $GCS_ENABLED"
echo "GCS_PROJECT_ID: $GCS_PROJECT_ID"
echo "GCS_BUCKET: $GCS_BUCKET"
echo "GCS_KEY_FILE: $GCS_KEY_FILE"

echo ""
echo "3. Checking service files..."
test -f app/Services/GoogleCloudStorageService.php && echo "✓ GoogleCloudStorageService.php exists" || echo "✗ GoogleCloudStorageService.php missing"
test -f app/Services/KycUploadService.php && echo "✓ KycUploadService.php exists" || echo "✗ KycUploadService.php missing"

echo ""
echo "4. Checking PHP syntax..."
php -l app/Services/GoogleCloudStorageService.php
php -l app/Services/KycUploadService.php

echo ""
echo "5. Testing GCS connection..."
php artisan tinker <<< "exit; app(\App\Services\GoogleCloudStorageService::class)->isEnabled() ? print('✓ GCS Connected') : print('✗ GCS Not Connected');"

echo ""
echo "=== Verification Complete ==="
```

---

**Last Updated**: April 19, 2026
**Status**: Ready for Deployment
