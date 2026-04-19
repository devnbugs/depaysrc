# Transfer Service System Documentation

## Overview

This document describes the comprehensive transfer service system including:
1. **Dpay Interbank Transfers** - Send money with phone number (default) or username resolution
2. **Local Bank Transfers** - Transfer to any bank account
3. **User-to-User Transfers** - Send money between internal users
4. **Transaction Split Feature** - Automatically split large transfers into compliance-friendly chunks

---

## 1. Dpay Interbank Transfer

### Features
- **Default Phone Resolution**: Resolves recipients by phone number (fastest)
- **Username Resolution**: Alternative recipient lookup by username
- **Local User Detection**: Detects if recipient is an internal user for instant transfer
- **External Bank Support**: Transfers to external bank accounts via Dpay API
- **Beneficiary Management**: Save frequently used recipients
- **Transaction History**: Track all transfers with detailed status

### Configuration

Add these environment variables to `.env`:

```env
DPAY_ENABLED=true
DPAY_API_KEY=your_dpay_api_key_here
DPAY_BASE_URL=https://api.dpay.ng/api/v1/
DPAY_MINIMUM=100
DPAY_MAXIMUM=1000000
```

### API Endpoints

#### 1. Resolve Recipient
**Endpoint**: `POST /dpay-transfer/resolve`

Resolves a recipient by phone number (default) or username.

```json
{
  "recipient": "08012345678",  // Phone or username
  "type": "phone"               // Options: "phone" (default), "username"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Recipient resolved successfully.",
  "data": {
    "resolved_by": "local_user|dpay",
    "account_name": "John Doe",
    "account_number": "1000001234",
    "phone": "08012345678",
    "bank_name": "Access Bank",
    "bank_code": "044"
  }
}
```

#### 2. Submit Transfer
**Endpoint**: `POST /dpay-transfer` or `POST /user/dpay-transfer`

Submits a new transfer request for preview.

```json
{
  "recipient": "08012345678",
  "type": "phone",
  "amount": 5000.50,
  "narration": "Payment for goods",
  "save_beneficiary": true
}
```

#### 3. Preview Transfer
**Endpoint**: `GET /dpay-transfer/preview` or `GET /user/dpay-transfer/preview`

Displays transfer preview before confirmation (requires session data from submit).

#### 4. Confirm Transfer
**Endpoint**: `POST /dpay-transfer/confirm` or `POST /user/dpay-transfer/confirm`

Confirms and processes the transfer.

### Usage Example (JavaScript)

```javascript
// Step 1: Resolve recipient
const resolveResponse = await fetch('/user/dpay-transfer/resolve', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrfToken,
  },
  body: JSON.stringify({
    recipient: '08012345678',
    type: 'phone'  // Default
  })
});

const resolved = await resolveResponse.json();

// Step 2: Submit transfer
const submitResponse = await fetch('/user/dpay-transfer', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded',
    'X-CSRF-TOKEN': csrfToken,
  },
  body: new URLSearchParams({
    recipient: '08012345678',
    type: 'phone',
    amount: 5000,
    narration: 'Test transfer',
    save_beneficiary: '1'
  })
});

// Redirects to preview page

// Step 3: Confirm from preview page
```

---

## 2. Local Bank Transfer with Transaction Split

### Features
- **Account Resolution**: Resolves bank account names before transfer
- **Automatic Splitting**: Transfers > 10,000 are split into manageable chunks
- **Transaction Split Settings**: Admin-configurable threshold and enable/disable
- **PIN Protection**: Optional PIN verification for security
- **Fee Calculation**: Automatic fee deduction
- **Beneficiary Tracking**: Saves frequently used recipients

### Transaction Split Feature

#### How It Works

When a transfer amount exceeds the configured threshold (default: 10,000):
- **15,000** → Split into: [10,000] + [5,000]
- **25,000** → Split into: [10,000] + [10,000] + [5,000]
- **10,000** → No split (exactly at threshold)
- **5,000** → No split (below threshold)

#### Admin Settings

**Route**: `/admin/settings/transaction-split`

```php
// Update transaction split settings
PUT /admin/settings/transaction-split

{
  "transaction_split_enabled": true,
  "transaction_split_threshold": 10000,
  "transaction_split_description": "Large transfers are automatically split..."
}
```

#### Get Split Information (API)
**Endpoint**: `POST /user/other-transfer-Fund/split-info`

```json
{
  "amount": 25000
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "total_amount": 25000,
    "chunk_size": 10000,
    "chunks": [10000, 10000, 5000],
    "chunk_count": 3,
    "requires_split": true,
    "currency_symbol": "₦",
    "currency_text": "NGN"
  }
}
```

### Local Transfer API Endpoints

#### 1. Get Transfer Form
**Endpoint**: `GET /other-transfer-Fund`

Displays local bank transfer form with banks list and split info.

#### 2. Resolve Account
**Endpoint**: `POST /other-transfer-Fund/resolve`

```json
{
  "bank_name": "Access Bank",
  "bank_code": "044",
  "account_number": "1234567890"
}
```

#### 3. Get Split Info
**Endpoint**: `POST /other-transfer-Fund/split-info`

Returns transaction split calculation for an amount.

#### 4. Submit Transfer
**Endpoint**: `POST /other-transfer-Fund`

Submits the transfer for processing (handles splits automatically).

---

## 3. User-to-User Transfer

### Features
- **Instant Transfers**: Money credited immediately
- **Zero Fees**: No charges for internal transfers
- **Beneficiary List**: Save and quickly transfer to saved users
- **Name Verification**: Shows recipient name before confirmation

### API Endpoints

#### 1. Get Transfer Form
**Endpoint**: `GET /transfer-Fund`

Shows user transfer form with saved beneficiaries.

#### 2. Submit Transfer
**Endpoint**: `POST /transfer-Fund`

```json
{
  "type": 1,                              // 1 = saved beneficiary, 0 = new
  "beneficiary": "1000001234",            // Account number
  "amount": 5000
}
```

OR with username:

```json
{
  "type": 0,
  "username": "johndoe",
  "amount": 5000
}
```

#### 3. Preview Transfer
**Endpoint**: `GET /transfer-Fund/preview`

Shows transfer preview before confirmation.

#### 4. Send Transfer
**Endpoint**: `POST /transfer-Fund/preview`

Processes the transfer immediately.

---

## Testing

### Running Feature Tests

```bash
# Run all transfer tests
php artisan test tests/Feature/TransferServiceApiTest.php

# Run specific test
php artisan test tests/Feature/TransferServiceApiTest.php --filter=test_dpay_transfer_submit_success

# Run with coverage
php artisan test --coverage tests/Feature/TransferServiceApiTest.php
```

### Test Cases Included

1. **Dpay Tests**
   - `test_dpay_resolve_with_phone` - Resolve by phone
   - `test_dpay_resolve_with_username` - Resolve by username
   - `test_dpay_transfer_submit_success` - Submit valid transfer
   - `test_dpay_transfer_submit_insufficient_balance` - Balance validation
   - `test_dpay_transfer_confirm` - Confirm transfer
   - `test_dpay_transfer_with_saved_beneficiary` - Save beneficiary
   - `test_dpay_preview_session_timeout` - Session expiration handling

2. **Local Transfer Tests**
   - `test_local_bank_transfer_resolve_account` - Account resolution
   - `test_transfer_history_retrieval` - Get transfer history

3. **Transaction Split Tests**
   - `test_local_transfer_split_info` - Get split calculation
   - `test_transaction_split_at_threshold` - Exactly at threshold
   - `test_transaction_split_below_threshold` - Below threshold
   - `test_transaction_split_odd_amount` - Odd amount splitting

4. **User-to-User Tests**
   - `test_user_to_user_transfer` - Valid transfer
   - `test_user_to_user_transfer_invalid_beneficiary` - Invalid recipient
   - `test_transfer_to_self_rejected` - Self-transfer rejection

5. **Admin Tests**
   - `test_admin_transaction_split_settings` - Update settings
   - `test_admin_test_split_calculation` - Test split endpoint

### Using Postman

Import the Postman collection template:

```json
{
  "name": "Transfer Service API",
  "items": [
    {
      "name": "Dpay - Resolve (Phone)",
      "request": {
        "method": "POST",
        "url": "{{base_url}}/user/dpay-transfer/resolve",
        "body": {
          "raw": {
            "recipient": "08012345678",
            "type": "phone"
          }
        }
      }
    },
    {
      "name": "Local Transfer - Get Split Info",
      "request": {
        "method": "POST",
        "url": "{{base_url}}/user/other-transfer-Fund/split-info",
        "body": {
          "raw": {
            "amount": "25000"
          }
        }
      }
    }
  ]
}
```

---

## Database Schema

### Transfer Model Fields
```php
$table->id();
$table->unsignedBigInteger('user_id');
$table->unsignedTinyInteger('method_id');  // 1=User2User, 2=LocalBank, 3=Dpay
$table->decimal('amount', 15, 2);
$table->decimal('charge', 15, 2);
$table->string('bank_name')->nullable();
$table->string('bank_code')->nullable();
$table->string('account_name')->nullable();
$table->string('account_number')->nullable();
$table->text('narration')->nullable();
$table->json('details')->nullable();
$table->json('meta')->nullable();
$table->unsignedTinyInteger('status');      // 0=Pending, 1=Approved, 2=Rejected
$table->string('reason')->nullable();
$table->string('trx', 50);
$table->timestamps();
```

### General Settings Extensions
```php
// Transaction Split Settings
$table->boolean('transaction_split_enabled')->default(true);
$table->decimal('transaction_split_threshold', 15, 2)->default(10000);
$table->text('transaction_split_description')->nullable();
```

---

## Error Handling

### Common Error Responses

**Insufficient Balance**
```json
{
  "message": "Insufficient balance for this transfer.",
  "errors": { "amount": ["..."] }
}
```

**Invalid Recipient**
```json
{
  "success": false,
  "message": "Unable to resolve recipient. Please verify and try again."
}
```

**Session Expired**
```json
{
  "message": "Transfer session expired. Please initiate a new transfer."
}
```

**Transfer Disabled**
```json
{
  "success": false,
  "message": "Local transfer is not enabled right now."
}
```

---

## Best Practices

### For Users
1. ✅ Verify recipient details before confirmation
2. ✅ Save frequently used beneficiaries
3. ✅ Keep PIN secure if enabled
4. ✅ Check transfer history regularly

### For Developers
1. ✅ Always validate request inputs
2. ✅ Use database transactions for consistency
3. ✅ Log all transfer attempts for audit
4. ✅ Test with various edge cases
5. ✅ Implement rate limiting for API endpoints
6. ✅ Handle Dpay API timeouts gracefully

### For Admins
1. ✅ Configure transaction split settings based on compliance needs
2. ✅ Set appropriate transfer limits
3. ✅ Monitor failed transfers
4. ✅ Regularly review transfer logs
5. ✅ Update bank list as needed

---

## Service Classes

### TransactionSplitService

```php
use App\Services\Transfers\TransactionSplitService;

// Calculate split for an amount
$split = TransactionSplitService::calculateOptimalSplit(25000);
// Returns: ['chunks' => [10000, 10000, 5000], 'chunk_count' => 3, ...]

// Get split description
$desc = TransactionSplitService::getDescription(25000);
// Returns: "Amount 25,000.00 will be split into 3 transfer(s) of 10,000.00 each"

// Verify split amounts sum correctly
$valid = TransactionSplitService::verify([10000, 10000, 5000], 25000);
// Returns: true
```

---

## Troubleshooting

### Dpay Transfer Not Working
- Verify `DPAY_API_KEY` is correctly set
- Check `DPAY_ENABLED=true` in `.env`
- Ensure API endpoint is accessible
- Review error logs for API response details

### Transaction Split Not Appearing
- Check if `transaction_split_enabled` is `true` in general settings
- Verify `transaction_split_threshold` is configured
- Clear application cache: `php artisan cache:clear`

### Account Resolution Failing
- Verify bank code is correct
- Check if bank is supported by provider
- Ensure account number format is valid (10 digits)

---

## Support & Maintenance

For issues or questions:
1. Check error logs: `storage/logs/laravel.log`
2. Review transfer history in admin panel
3. Test with Postman collection
4. Run feature tests: `php artisan test`
5. Contact support with transaction ID (TRX)

---

**Last Updated**: April 19, 2026  
**Version**: 1.0.0
