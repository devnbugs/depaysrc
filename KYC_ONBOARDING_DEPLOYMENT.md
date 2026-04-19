# KYC Onboarding System - Deployment Checklist

## Pre-Deployment (Development)

- [ ] Run migration: `php artisan migrate`
- [ ] Register middleware in `app/Http/Kernel.php` or `bootstrap/app.php`
- [ ] Include onboarding routes in `routes/web.php`
- [ ] Configure Kora credentials in `config/services.php` and `.env`
- [ ] Review `KycVerificationService.php` - adjust level limits if needed
- [ ] Test onboarding flow as new user locally
- [ ] Test transfer validation in your transfer controller
- [ ] Clear cache: `php artisan config:cache`

## Code Changes Required

### 1. Update routes/web.php

Add this import at the top:
```php
<?php

use Illuminate\Support\Facades\Route;

// Add this line with your other route files
require base_path('routes/onboarding.php');
```

### 2. Update app/Http/Kernel.php (Laravel 10 and below)

In the `$middlewareGroups['web']` array, add:
```php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        // ... other middleware ...
        \App\Http\Middleware\OnboardingMiddleware::class, // ← Add this line
    ],
];
```

**OR for Laravel 11+** in `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\OnboardingMiddleware::class, // ← Add this
    ]);
})
```

### 3. Update config/services.php

Add Kora configuration:
```php
'kora' => [
    'secret_key' => env('KORA_SECRET_KEY'),
    'public_key' => env('KORA_PUBLIC_KEY'),
    'base_url' => env('KORA_BASE_URL', 'https://api.korapay.com/'),
],
```

### 4. Update .env

Add these variables:
```
KORA_SECRET_KEY=your_kora_secret_key
KORA_PUBLIC_KEY=your_kora_public_key
KORA_BASE_URL=https://api.korapay.com/
```

### 5. Update Your Transfer Controllers

Add the trait to any controller that handles transfers:
```php
use App\Http\Traits\ValidatesUserTransfers;

class TransferController extends Controller
{
    use ValidatesUserTransfers;
    
    // Your existing methods...
    
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Add this check
        $validation = $this->validateTransferRequest($user, $request->amount);
        if ($validation) return $validation;
        
        // Rest of your code...
    }
}
```

## Staging Environment

- [ ] Deploy code changes
- [ ] Run migrations: `php artisan migrate`
- [ ] Test onboarding flow completely
- [ ] Test transfer validation with different user levels
- [ ] Verify Kora API integration works
- [ ] Test deposit requirement enforcement
- [ ] Check all logs in `storage/logs/`
- [ ] Verify database changes
- [ ] Test with real Kora API (staging/test credentials)

## Production Deployment

### Before Going Live

- [ ] Backup production database
- [ ] Test migrations in staging first
- [ ] Have rollback plan ready
- [ ] Verify Kora credentials are correct
- [ ] Test at least 5 complete onboarding flows
- [ ] Verify all transfer limits are correct
- [ ] Check audit logging is working

### Deployment Steps

1. **Deploy Code**
   ```bash
   git pull origin main
   composer install --no-dev
   ```

2. **Backup Database**
   ```bash
   php artisan backup:run
   ```

3. **Run Migrations**
   ```bash
   php artisan migrate --force
   ```

4. **Clear Cache**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Verify Installation**
   ```bash
   php artisan config:show kora
   # Should show your Kora configuration
   ```

6. **Monitor Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Post-Deployment Verification

- [ ] Users can access onboarding page
- [ ] Onboarding flow works end-to-end
- [ ] Transfer validation is enforced
- [ ] Tier limits are applied correctly
- [ ] Audit logs are being created
- [ ] Kora integration is working
- [ ] No errors in application logs

## Rollback Plan

If something goes wrong:

1. **Rollback Code**
   ```bash
   git revert <commit-hash>
   git push origin main
   ```

2. **Rollback Database** (if needed)
   ```bash
   php artisan migrate:rollback
   ```

3. **Clear Cache**
   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

## Files Deployed

### New Files
- `app/Services/KycVerificationService.php`
- `app/Services/UserTransferValidationService.php`
- `app/Http/Controllers/OnboardingController.php`
- `app/Http/Middleware/OnboardingMiddleware.php`
- `app/Http/Traits/ValidatesUserTransfers.php`
- `app/Http/Controllers/Examples/ExampleTransferController.php`
- `routes/onboarding.php`
- `database/migrations/2026_04_19_000001_*.php`
- `resources/views/onboarding/index.blade.php`
- `KYC_ONBOARDING_IMPLEMENTATION.md`
- `KYC_ONBOARDING_QUICK_START.md`
- `KYC_ONBOARDING_DEPLOYMENT.md`

### Modified Files
- `app/Services/KoraService.php` (added liveness methods)
- `routes/web.php` (include onboarding routes)
- `app/Http/Kernel.php` or `bootstrap/app.php` (register middleware)
- `config/services.php` (add Kora config)
- `.env` (add Kora credentials)

## Monitoring After Deployment

### Daily Checks
- Check application logs for errors
- Verify onboarding completion rate
- Monitor transfer validation rejections
- Check Kora API integration status

### Weekly Checks
- Review tier distribution of users
- Check deposit requirement enforcement
- Verify transfer limits are being applied
- Audit user limit violations

### Key Metrics
```
- Users who started onboarding
- Users who completed onboarding
- Average time to complete onboarding
- Users by verification level
- Failed KYC attempts
- Deposit requirement compliance
- Transfer limit violations
```

## Database Verification

Run these queries to verify data integrity:

```sql
-- Check new columns exist
SHOW COLUMNS FROM users LIKE 'kyc%';

-- Check user distribution by level
SELECT kyc_verification_level, COUNT(*) FROM users GROUP BY kyc_verification_level;

-- Check liveness completion
SELECT COUNT(*) FROM users WHERE kyc_liveness_verified = true;

-- Check onboarding completion
SELECT COUNT(*) FROM users WHERE onboarding_completed_at IS NOT NULL;
```

## Issues and Resolutions

### Issue: Middleware not enforcing onboarding
**Resolution:**
- Verify middleware is registered in Kernel.php
- Check route names in OnboardingMiddleware match your routes
- Restart PHP-FPM: `sudo systemctl restart php-fpm`

### Issue: Kora API returning 401
**Resolution:**
- Verify `KORA_SECRET_KEY` in `.env`
- Check API credentials are correct
- Ensure API key has required permissions

### Issue: Users can bypass onboarding
**Resolution:**
- Check middleware chain order
- Verify `OnboardingMiddleware` is before other auth middleware
- Clear route cache: `php artisan route:cache`

### Issue: Transfer limit not enforced
**Resolution:**
- Verify trait is used in transfer controller
- Check user's `kyc_verification_level` in database
- Run validation method before processing transfer

## Support and Escalation

### Level 1: Check Logs
- `storage/logs/laravel.log`
- `storage/logs/error.log`
- Check database audit fields

### Level 2: Review Implementation
- Compare your code with `ExampleTransferController.php`
- Verify middleware registration
- Check route names

### Level 3: Kora Support
- Contact Kora support for API issues
- Verify credentials with Kora
- Check API status page

## Maintenance Tasks

### Monthly
- [ ] Review KYC completion rate
- [ ] Check tier distribution
- [ ] Verify no data integrity issues
- [ ] Review failed verification attempts

### Quarterly
- [ ] Adjust level limits if needed
- [ ] Review security audit logs
- [ ] Update deposit requirements if needed
- [ ] Performance optimization

## Sign-Off

- [ ] QA has tested and approved
- [ ] Product team has signed off
- [ ] Database backup confirmed
- [ ] Rollback plan tested
- [ ] Monitoring configured
- [ ] Support team trained
- [ ] Documentation is current

---

**Deployment Date:** _______________
**Deployed By:** _______________
**Verified By:** _______________
**Notes:** _______________
