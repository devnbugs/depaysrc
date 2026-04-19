# KYC Onboarding Implementation Verification Checklist

## ✅ Files Verification

### Services Created
- [ ] `app/Services/KycVerificationService.php` exists
- [ ] `app/Services/UserTransferValidationService.php` exists
- [ ] `app/Services/KoraService.php` enhanced with liveness methods

### Controllers & Middleware Created
- [ ] `app/Http/Controllers/OnboardingController.php` exists
- [ ] `app/Http/Middleware/OnboardingMiddleware.php` exists
- [ ] `app/Http/Controllers/Examples/ExampleTransferController.php` exists

### Utilities Created
- [ ] `app/Http/Traits/ValidatesUserTransfers.php` exists

### Routes Created
- [ ] `routes/onboarding.php` exists with 9 endpoints

### Database
- [ ] `database/migrations/2026_04_19_000001_*.php` exists

### Views Created
- [ ] `resources/views/onboarding/index.blade.php` exists

### Documentation Created
- [ ] `README_KYC_ONBOARDING.md` - Main index
- [ ] `KYC_ONBOARDING_QUICK_START.md` - Quick setup
- [ ] `KYC_ONBOARDING_IMPLEMENTATION.md` - Full guide
- [ ] `KYC_ONBOARDING_DEPLOYMENT.md` - Deployment checklist
- [ ] `KYC_ONBOARDING_SYSTEM_SUMMARY.md` - System overview

## 🔧 Setup Verification

### Step 1: Migration
```bash
php artisan migrate
```
- [ ] Migration runs without errors
- [ ] Command completes successfully
- [ ] No existing column warnings

### Step 2: Middleware Registration

**For Laravel 11+ (bootstrap/app.php):**
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\OnboardingMiddleware::class,
    ]);
})
```
- [ ] Added to withMiddleware callback
- [ ] No syntax errors in bootstrap/app.php
- [ ] File saved successfully

**OR for Laravel 10 and below (app/Http/Kernel.php):**
```php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware ...
        \App\Http\Middleware\OnboardingMiddleware::class,
    ],
];
```
- [ ] Added to $middlewareGroups['web'] array
- [ ] Syntax is correct
- [ ] File saved successfully

### Step 3: Routes Registration

In `routes/web.php`, add:
```php
require base_path('routes/onboarding.php');
```
- [ ] Line added to routes/web.php
- [ ] Location is after other route definitions
- [ ] File saved and can be executed
- [ ] Run: `php artisan route:list` shows onboarding routes

### Step 4: Configuration

In `config/services.php`:
```php
'kora' => [
    'secret_key' => env('KORA_SECRET_KEY'),
    'public_key' => env('KORA_PUBLIC_KEY'),
    'base_url' => env('KORA_BASE_URL', 'https://api.korapay.com/'),
],
```
- [ ] Config section exists
- [ ] Array structure is correct
- [ ] Uses env() for keys

In `.env`:
```
KORA_SECRET_KEY=your_secret_key
KORA_PUBLIC_KEY=your_public_key
KORA_BASE_URL=https://api.korapay.com/
```
- [ ] Values added to .env
- [ ] Not committed to version control
- [ ] Values are populated with actual keys

## 🗄️ Database Verification

Run these SQL queries to verify:

```sql
-- Check columns exist
SHOW COLUMNS FROM users LIKE 'kyc_%';
SHOW COLUMNS FROM users LIKE 'onboarding_%';
SHOW COLUMNS FROM users LIKE 'transfer_%';
SHOW COLUMNS FROM users LIKE 'deposit_%';
```

- [ ] `kyc_verification_level` column exists
- [ ] `kyc_liveness_verified` column exists
- [ ] `kyc_liveness_verified_at` column exists
- [ ] `kora_liveness_id` column exists
- [ ] `kora_liveness_status` column exists
- [ ] `required_kyc_fields_completed` column exists
- [ ] `onboarding_step` column exists
- [ ] `transfer_limit` column exists
- [ ] `account_creation_limit` column exists
- [ ] `deposit_requirement_for_level_3` column exists
- [ ] `total_deposited` column exists

## 🔗 Application Verification

### Test Onboarding Flow

1. **Create Test User**
```bash
php artisan tinker
>>> User::factory()->create(['email' => 'test@example.com'])
```
- [ ] Test user created successfully

2. **Login and Access Onboarding**
- [ ] Login with test user
- [ ] Automatically redirected to `/onboarding`
- [ ] Dashboard shows progress bar
- [ ] Shows all 4 steps

3. **Complete Personal Info Step**
- [ ] Form accepts first name, last name, phone, address
- [ ] Submit works without errors
- [ ] Advances to next step
- [ ] Data saved in database

4. **Complete Identity Verification**
- [ ] Form shows BVN/NIN selection
- [ ] Can enter test BVN
- [ ] Submit processes request
- [ ] Advances to liveness step
- [ ] User level updates to Level 2

5. **Complete Liveness Check**
- [ ] Form shows liveness initiation button
- [ ] Can start liveness check
- [ ] Status page shows progress
- [ ] After completion: User reaches Level 3
- [ ] Can access all features

6. **Completion Page**
- [ ] Shows success message
- [ ] Displays user's level and limits
- [ ] Button to go to dashboard works
- [ ] User can access protected routes

### Test Transfer Validation

1. **In Transfer Controller**
- [ ] Add trait: `use ValidatesUserTransfers;`
- [ ] Use method: `$this->validateTransferRequest($user, $amount);`
- [ ] Syntax is correct
- [ ] No errors on page load

2. **Test Different Levels**
- [ ] Level 0: Cannot transfer (redirects to onboarding)
- [ ] Level 1: Can transfer up to ₦50k, blocks ₦100k
- [ ] Level 2: Can transfer up to ₦500k
- [ ] Level 3: Can transfer up to ₦5M

3. **Test Deposit Requirement**
- [ ] User at Level 2 cannot access Level 3 features
- [ ] After ₦400 deposit: User upgrades to Level 3
- [ ] Can now access full features

## 🧪 Test Endpoints

### Using curl or Postman

```bash
# Get onboarding status (requires auth)
GET http://localhost:8000/onboarding
Authorization: Bearer {token}

# Should return: 200 OK with onboarding dashboard

# Submit personal info
POST http://localhost:8000/onboarding/personal-info
Authorization: Bearer {token}
Content-Type: application/json
{
  "firstname": "John",
  "lastname": "Doe",
  "mobile": "+2348012345678",
  "whatsapp_phone": "+2348012345678",
  "address": {
    "address": "123 Main St",
    "state": "Lagos",
    "city": "Lagos",
    "zip": "101242",
    "country": "Nigeria"
  }
}

# Should return: 200 OK with next_step
```

- [ ] All endpoints respond with correct status codes
- [ ] Responses include expected fields
- [ ] Authentication is enforced
- [ ] Validation errors are returned for bad data

## 📊 Data Verification

Query database to verify data:

```sql
-- Check user levels
SELECT id, email, kyc_verification_level, onboarding_step FROM users WHERE id = 1;

-- Check onboarding completion
SELECT id, email, onboarding_completed_at FROM users WHERE onboarding_completed_at IS NOT NULL;

-- Check liveness status
SELECT id, email, kyc_liveness_verified, kyc_liveness_verified_at FROM users WHERE kyc_liveness_verified = 1;

-- Check deposits
SELECT id, email, total_deposited FROM users WHERE total_deposited > 0;
```

- [ ] User levels are being set correctly (0-3)
- [ ] `onboarding_completed_at` is set after completion
- [ ] `kyc_liveness_verified_at` is set after liveness
- [ ] `total_deposited` is being tracked

## 🔐 Security Verification

- [ ] Cannot access other routes without completing onboarding
- [ ] Cannot bypass onboarding with direct URL
- [ ] Middleware properly redirects incomplete profiles
- [ ] Transfer validation actually blocks invalid transfers
- [ ] Cannot create accounts beyond limit
- [ ] Audit logs are being created in storage/logs/

## 📝 Logging Verification

Check `storage/logs/laravel.log`:

```bash
tail -f storage/logs/laravel.log
```

- [ ] Onboarding steps are logged
- [ ] Identity verification attempts are logged
- [ ] Liveness checks are logged
- [ ] Transfer attempts are logged (success and failures)
- [ ] No error entries for normal operations

## 🧹 Cleanup & Optimization

- [ ] No test data left in database
- [ ] Cache cleared: `php artisan config:cache`
- [ ] Routes cached (optional): `php artisan route:cache`
- [ ] Views cached (optional): `php artisan view:cache`
- [ ] Storage links created if needed

## 📋 Pre-Production Checklist

- [ ] All verification steps above complete
- [ ] Tested with multiple users
- [ ] Transfer limits enforced correctly
- [ ] Deposit requirement working
- [ ] Audit logging working
- [ ] No console errors in browser
- [ ] No errors in Laravel logs
- [ ] Performance is acceptable

## 🚀 Production Deployment

- [ ] Database backup created
- [ ] Code committed and pushed
- [ ] Migration run on production: `php artisan migrate --force`
- [ ] Cache cleared: `php artisan config:cache`
- [ ] Test with production credentials
- [ ] Monitor logs for first 24 hours
- [ ] Verify first users complete onboarding successfully

## 🎯 Success Criteria

All of the following must be true:

- [ ] ✅ New users are redirected to onboarding
- [ ] ✅ Onboarding flow completes without errors
- [ ] ✅ User levels are assigned correctly
- [ ] ✅ Transfer validation works
- [ ] ✅ Deposit requirement is enforced
- [ ] ✅ Tier limits are applied
- [ ] ✅ Audit logging works
- [ ] ✅ No breaking changes to existing features
- [ ] ✅ All documentation is accurate
- [ ] ✅ System is ready for production

## 📞 Troubleshooting During Verification

### Migration fails
```bash
# Check migration status
php artisan migrate:status

# Rollback if needed
php artisan migrate:rollback

# Run again
php artisan migrate
```

### Middleware not redirecting
- Verify registered in Kernel/bootstrap
- Check route names in middleware
- Clear route cache: `php artisan route:clear`

### Routes not found
- Verify `require base_path('routes/onboarding.php')` in web.php
- Check onboarding.php file exists
- Run: `php artisan route:list | grep onboarding`

### Kora API errors
- Verify `KORA_SECRET_KEY` is set
- Check credentials are correct
- Test with curl: `curl -H "Authorization: Bearer KEY" https://api.korapay.com/test`

### Transfer validation not working
- Verify trait is used in controller
- Check `validateTransferRequest()` is called
- Verify user level is set in database
- Run query: `SELECT kyc_verification_level FROM users WHERE id = 1;`

## 📊 Sign-Off

When all checkboxes are complete:

**Verified By:** _________________ **Date:** _________

**Notes:** ____________________________________________________________

**Status:** ✅ PRODUCTION READY

---

**Keep this checklist for future reference and re-deployment verification.**
