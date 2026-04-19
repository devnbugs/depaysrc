# KYC Onboarding and Tiered Limits Implementation Guide

## Overview

This implementation provides a complete KYC onboarding system with:
- **Forced Onboarding** on first login or incomplete profiles
- **Multi-Step Verification**: Personal Info → Identity (BVN/NIN) → Liveness Check
- **Kora Integration**: Auto-verification via Kora Identity + Liveness checks
- **Tiered User Levels**: 4 levels (None, Basic, Advanced, Premium) with different limits
- **Deposit Requirements**: ₦400 minimum for Level 3 (Premium) access
- **Transfer Limits**: Tier-based daily/monthly limits and features

## Architecture

### Components

1. **KycVerificationService** - Core KYC management service
2. **UserTransferValidationService** - Transfer eligibility validation
3. **OnboardingController** - Handles onboarding flow
4. **OnboardingMiddleware** - Enforces onboarding on first login
5. **ValidatesUserTransfers Trait** - Easy integration into any controller
6. **Database Migration** - Adds KYC fields to users table

### Database Changes

The migration adds these fields to the `users` table:

```
- kyc_verification_level (0=None, 1=Basic, 2=Advanced, 3=Premium)
- kyc_liveness_verified (boolean)
- kyc_liveness_verified_at (timestamp)
- kora_liveness_id (string)
- kora_liveness_status (string)
- required_kyc_fields_completed (json)
- onboarding_step (string)
- transfer_limit (decimal)
- account_creation_limit (integer)
- deposit_requirement_for_level_3 (decimal)
- total_deposited (decimal)
```

## Verification Levels

### Level 0: Unverified (None)
- Transfer Limit: ₦10,000/day
- Account Creation: 0 accounts
- Can Receive: Yes
- Can Transfer: No
- Full Features: No

### Level 1: Basic
- Transfer Limit: ₦50,000/day
- Account Creation: 1 account
- Can Receive: Yes
- Can Transfer: Yes (basic)
- Full Features: No

### Level 2: Advanced
- Transfer Limit: ₦500,000/day
- Account Creation: 2 accounts
- Can Receive: Yes
- Can Transfer: Yes
- Full Features: No

### Level 3: Premium
- Transfer Limit: ₦5,000,000/day
- Account Creation: 5 accounts
- Can Receive: Yes
- Can Transfer: Yes (full)
- Full Features: Yes ✓
- **Requirement**: ₦400+ deposited + Liveness verified

## Installation Steps

### 1. Run Migration

```bash
php artisan migrate
```

This creates the necessary columns in the `users` table.

### 2. Register Middleware

In `app/Http/Kernel.php`, add the middleware to the `web` middleware group:

```php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware
        \App\Http\Middleware\OnboardingMiddleware::class,
    ],
];
```

Or register in `bootstrap/app.php` (Laravel 11+):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        OnboardingMiddleware::class,
    ]);
})
```

### 3. Register Routes

In `routes/web.php`, include the onboarding routes:

```php
require base_path('routes/onboarding.php');
```

### 4. Update Service Providers

In your app's service provider, bind the services:

```php
$this->app->singleton(KycVerificationService::class, function ($app) {
    return new KycVerificationService($app->make(KoraService::class));
});

$this->app->singleton(UserTransferValidationService::class, function ($app) {
    return new UserTransferValidationService($app->make(KycVerificationService::class));
});
```

### 5. Publish Views (Optional)

Copy the onboarding views from `resources/views/onboarding/` to your Laravel app.

## Usage in Controllers

### Example: Transfer Controller

```php
<?php

namespace App\Http\Controllers;

use App\Http\Traits\ValidatesUserTransfers;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    use ValidatesUserTransfers;

    public function initiateTransfer(Request $request)
    {
        $user = auth()->user();
        $amount = $request->input('amount');

        // Check transfer eligibility
        $validationResult = $this->validateTransferRequest($user, $amount, 'bank_transfer');
        if ($validationResult) {
            return $validationResult; // Returns error response if not eligible
        }

        // Proceed with transfer
        // ...

        // Log successful attempt
        $this->logTransferAttempt($user, $amount, 'bank_transfer', true);
    }
}
```

### Example: Virtual Account Controller

```php
public function createVirtualAccount(Request $request)
{
    $user = auth()->user();

    // Check account creation eligibility
    $validation = $this->transferValidationService()->validateAccountCreation($user);
    if (!$validation['allowed']) {
        return response()->json(['success' => false, 'errors' => $validation['errors']], 422);
    }

    // Create account
    // ...
}
```

### Checking User Limits

```php
// Get user's eligibility summary
$eligibility = $this->getTransferEligibility($user);

// Check specific features
if ($this->getTransferEligibility($user)['full_features_unlocked']) {
    // User has Level 3 access
}

// Get profile completion
$completionPercentage = $this->getProfileCompletionPercentage($user);
```

## API Responses

### Transfer Validation Response

```json
{
  "allowed": false,
  "errors": [
    "You need to deposit ₦400 to unlock full transfer features. Currently deposited: ₦0"
  ],
  "warnings": [],
  "limit": 50000,
  "level": "Basic",
  "level_code": 1,
  "user_id": 123
}
```

### User Eligibility Response

```json
{
  "eligible": true,
  "level": "Advanced",
  "level_code": 2,
  "transfer_limit": 500000,
  "daily_limit": 500000,
  "can_transfer": true,
  "can_create_account": true,
  "full_features_unlocked": false,
  "deposit_required_for_level_3": true,
  "deposit_amount": 400,
  "deposit_completed": 0,
  "deposit_remaining": 400,
  "liveness_verified": false,
  "identity_verified": true,
  "onboarding_complete": false
}
```

## Onboarding Flow

### Step 1: Personal Information
- User submits: First Name, Last Name, Phone, WhatsApp, Address
- Validates and stores in user profile

### Step 2: Identity Verification
- User submits BVN or NIN
- System verifies via Kora Identity API
- Auto-fills: Verified name, DOB, Gender, Address
- Upgrades to Level 2 (Advanced)

### Step 3: Liveness Check
- User initiates Kora Liveness verification
- System tracks liveness completion
- On completion: Upgrades to Level 3 (Premium) if deposit requirement met

### Step 4: Complete
- Shows user's verification level and limits
- Provides summary of available features

## Kora Integration

### Service Methods

```php
// In KoraService
$kora->verifyBvn($bvn, $secretKey);          // Verify BVN
$kora->verifyNin($nin, $secretKey);          // Verify NIN
$kora->initiateLiveness($userId, $fullName, $secretKey); // Start liveness
$kora->checkLivenessStatus($livenessId, $secretKey);     // Check status
$kora->getLivenessDetails($livenessId, $secretKey);      // Get details
```

### Configuration

Ensure these are set in your `.env`:

```
KORA_SECRET_KEY=your_secret_key
KORA_BASE_URL=https://api.korapay.com/
```

## Deposit Requirement

The system requires ₦400 minimum deposit to unlock Level 3 (Premium) features:

- Transfer ₦400 to any Kora funding channel
- System tracks deposits via `Deposit` model
- Auto-upgrades user to Level 3 when requirement is met
- Check with: `$kycService->checkDepositRequirement($user)`

## Customization

### Change Tier Limits

In `KycVerificationService`, modify the `$levelLimits` array:

```php
private $levelLimits = [
    self::LEVEL_BASIC => [
        'transfer_limit' => 100000,  // Change to ₦100k
        'daily_limit' => 100000,
        'account_creation_limit' => 2,  // Change to 2 accounts
        // ...
    ],
    // ...
];
```

### Change Deposit Requirement

In user's profile or settings:

```php
$user->update([
    'deposit_requirement_for_level_3' => 500  // Change to ₦500
]);
```

### Add Custom Validation

Extend `UserTransferValidationService`:

```php
public function validateCustomFeature(User $user): array
{
    $errors = [];
    
    if (!$user->has_custom_requirement) {
        $errors[] = 'Custom requirement not met';
    }
    
    return [
        'allowed' => empty($errors),
        'errors' => $errors,
    ];
}
```

## Audit & Logging

All transfer attempts are logged in the application logs:

```
[2024-04-19 10:30:45] local.WARNING: Transfer attempt denied {
  "user_id": 123,
  "amount": 1000000,
  "type": "full_transfer",
  "allowed": false,
  "reason": ["Amount exceeds limit", "Liveness not verified"]
}
```

## Testing

### Development/Staging Only

The onboarding controller provides a test route to skip steps:

```bash
POST /onboarding/skip-step

# Returns next step (local/staging only)
```

### Manual Testing

1. Create test user
2. Login (redirects to onboarding)
3. Fill personal info
4. Enter BVN (use test BVN from Kora docs)
5. Complete liveness check
6. User reaches Level 3

## Troubleshooting

### Middleware Not Redirecting
- Ensure middleware is registered in `app/Http/Kernel.php` or `bootstrap/app.php`
- Check route names match exactly

### Kora API Errors
- Verify `KORA_SECRET_KEY` in `.env`
- Check Kora API status
- Review error logs in `storage/logs/`

### User Not Upgrading to Level 3
- Verify `kyc_liveness_verified_at` is set
- Check `total_deposited` >= 400
- Both conditions must be true for auto-upgrade

### Views Not Showing
- Publish views: `php artisan vendor:publish --tag=onboarding-views`
- Or copy from `resources/views/onboarding/`

## Security Considerations

1. **Onboarding Bypass**: Middleware prevents access to protected routes until onboarding complete
2. **Identity Verification**: Uses Kora's verified API endpoint
3. **Liveness Check**: Prevents spoofing with face detection
4. **Deposit Tracking**: Verified deposits tracked from payment processors
5. **Audit Logging**: All transfer attempts logged with user, amount, reason

## Files Created/Modified

### New Files
- `app/Services/KycVerificationService.php` - Core KYC service
- `app/Services/UserTransferValidationService.php` - Transfer validation
- `app/Http/Controllers/OnboardingController.php` - Onboarding flow
- `app/Http/Middleware/OnboardingMiddleware.php` - Onboarding enforcement
- `app/Http/Traits/ValidatesUserTransfers.php` - Controller trait
- `routes/onboarding.php` - Onboarding routes
- `database/migrations/2026_04_19_000001_...` - Database migration
- `resources/views/onboarding/index.blade.php` - UI views

### Modified Files
- `app/Services/KoraService.php` - Added liveness methods
- (Update `routes/web.php` to include onboarding routes)
- (Update `app/Http/Kernel.php` to register middleware)

## Next Steps

1. ✅ Run migration: `php artisan migrate`
2. ✅ Register middleware in Kernel
3. ✅ Include onboarding routes in `routes/web.php`
4. ✅ Update transfer controllers to use trait
5. ✅ Test onboarding flow
6. ✅ Deploy to production

## Support

For issues or questions:
- Check audit logs: `storage/logs/laravel.log`
- Verify Kora API status
- Review KYC service documentation above
- Contact support team
