# KYC Onboarding System - Quick Start Guide

## 5-Minute Setup

### Step 1: Run Migration (1 minute)

```bash
php artisan migrate
```

This adds KYC fields to the `users` table.

### Step 2: Register Middleware (2 minutes)

**Option A: Laravel 11+ (bootstrap/app.php)**

Open `bootstrap/app.php` and add to the `withMiddleware` callback:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\OnboardingMiddleware::class,
    ]);
})
```

**Option B: Laravel 10 and below (app/Http/Kernel.php)**

Add to the `$middlewareGroups['web']` array:

```php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware ...
        \App\Http\Middleware\OnboardingMiddleware::class,
    ],
];
```

### Step 3: Register Routes (1 minute)

In `routes/web.php`, add this line:

```php
<?php

use Illuminate\Support\Facades\Route;

// ... existing routes ...

// Include onboarding routes
require base_path('routes/onboarding.php');

// ... rest of your routes ...
```

### Step 4: Configure Services (1 minute)

Update `config/services.php` with Kora credentials:

```php
'kora' => [
    'secret_key' => env('KORA_SECRET_KEY'),
    'base_url' => env('KORA_BASE_URL', 'https://api.korapay.com/'),
    'public_key' => env('KORA_PUBLIC_KEY'),
],
```

Add to your `.env`:

```
KORA_SECRET_KEY=your_secret_key_here
KORA_PUBLIC_KEY=your_public_key_here
```

## What Happens Now

✅ Users are automatically redirected to onboarding on first login
✅ They cannot access other routes until they complete onboarding
✅ Onboarding includes 3 steps:
  1. Personal information (name, address, phone)
  2. Identity verification (BVN/NIN via Kora)
  3. Liveness check (Kora liveness)
✅ User verification levels are automatically managed
✅ Transfer limits are enforced based on verification level

## Testing

1. **Create a test user** and login
2. You'll be **redirected to onboarding**
3. Fill in personal information
4. Use test BVN: `12345678901` (or valid test number from Kora docs)
5. Complete liveness check
6. You'll reach **Level 3 (Premium)** with full features

### Development: Skip Steps

In local/staging, you can skip onboarding steps:

```bash
curl -X POST http://localhost:8000/onboarding/skip-step \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Validation in Controllers

### Option 1: Quick Check (Recommended)

```php
use App\Http\Traits\ValidatesUserTransfers;

class TransferController extends Controller
{
    use ValidatesUserTransfers; // ← Add this

    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Validate transfer
        $validation = $this->validateTransferRequest($user, $request->amount);
        if ($validation) return $validation; // Returns error if not eligible
        
        // Proceed with transfer...
    }
}
```

### Option 2: Manual Check

```php
use App\Services\KycVerificationService;

class TransferController extends Controller
{
    public function store(Request $request, KycVerificationService $kycService)
    {
        $user = auth()->user();
        
        // Check eligibility
        $canTransfer = $kycService->canTransfer($user, $request->amount);
        if (!$canTransfer['allowed']) {
            return response()->json(['error' => $canTransfer['reason']], 422);
        }
        
        // Proceed...
    }
}
```

## User Limits by Level

| Level | Name | Transfer Limit | Accounts | Full Access |
|-------|------|---|---|---|
| 0 | Unverified | ₦10k | 0 | ❌ |
| 1 | Basic | ₦50k | 1 | ❌ |
| 2 | Advanced | ₦500k | 2 | ❌ |
| 3 | Premium | ₦5M | 5 | ✅ |

**Level 3 Requirements:**
- ₦400+ deposited
- Liveness verified
- Identity verified

## API Endpoints

All endpoints require authentication.

### Get Onboarding Status
```
GET /onboarding
```

### Get Current Step
```
GET /onboarding/{step}
```

Where step is: `personal-info`, `identity-verification`, or `liveness-check`

### Submit Personal Info
```
POST /onboarding/personal-info
Body: {firstname, lastname, mobile, whatsapp_phone, address}
```

### Verify Identity
```
POST /onboarding/identity-verification
Body: {identification_type: "bvn"|"nin", identification_number}
```

### Start Liveness
```
POST /onboarding/liveness-check/initiate
```

### Check Liveness Status
```
GET /onboarding/liveness-check/status
```

### Complete Onboarding
```
GET /onboarding/complete
```

## Customizing Limits

Edit `KycVerificationService.php`:

```php
private $levelLimits = [
    self::LEVEL_BASIC => [
        'transfer_limit' => 100000,  // Change to ₦100k
        'account_creation_limit' => 3,  // Change to 3 accounts
        // ...
    ],
    // ...
];
```

## Troubleshooting

### "User stuck on onboarding page"
- Check middleware is registered
- Check `onboarding_completed_at` is set after completion
- Clear browser cache and try again

### "Kora verification failing"
- Verify `KORA_SECRET_KEY` is set in `.env`
- Check Kora API status
- Test BVN format (usually 11 digits)

### "Transfer limits not working"
- Ensure trait is used in transfer controller
- Check user's `kyc_verification_level` in database
- Run validation method before allowing transfer

### "Liveness not progressing"
- Check `kora_liveness_status` in database
- Verify user completed liveness in Kora's system
- Check application logs in `storage/logs/`

## File Locations

All new files are in these locations:

```
app/Services/
  ├── KycVerificationService.php (Core KYC logic)
  ├── UserTransferValidationService.php (Transfer validation)
  └── KoraService.php (Updated - Liveness methods added)

app/Http/
  ├── Controllers/
  │   └── OnboardingController.php (Onboarding flow)
  ├── Middleware/
  │   └── OnboardingMiddleware.php (Enforcement)
  └── Traits/
      └── ValidatesUserTransfers.php (Controller helper)

routes/
  └── onboarding.php (Onboarding routes)

database/migrations/
  └── 2026_04_19_000001_*.php (Database schema)

resources/views/onboarding/
  └── index.blade.php (Main onboarding view)
```

## Next Steps

1. ✅ Follow 4-step setup above
2. ✅ Test onboarding flow as a new user
3. ✅ Add trait to your transfer controllers
4. ✅ Deploy to staging/production

## Questions?

Check these files for more details:
- `KYC_ONBOARDING_IMPLEMENTATION.md` - Complete implementation guide
- `app/Http/Controllers/Examples/ExampleTransferController.php` - Implementation examples
- `app/Services/KycVerificationService.php` - Service documentation

---

**Status:** ✅ Production Ready
**Last Updated:** 2024-04-19
