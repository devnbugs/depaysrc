# Phone Number Login & Password Reset Implementation

## Overview

The authentication system has been enhanced to support phone number login and password reset in addition to email and username. Users can now use any of the following to login or reset their password:

- **Email Address** - Traditional email login
- **Username** - User-defined username
- **Phone Number** - Mobile phone number (international format supported)

---

## Changes Made

### 1. Web Authentication Controllers Updated

#### ForgotPasswordController.php
- **Enhancement**: Now detects phone numbers and looks up users by mobile field
- **Detection Logic**: Recognizes phone numbers by:
  - Containing only digits, +, -, spaces, or parentheses
  - Having at least 10 digits
- **Error Messages**: Now shows specific error for each field type (Email, Phone Number, Username)

#### LoginController.php
- **Enhancement**: `findUsername()` method now detects phone numbers
- **Detection Logic**: Same as password reset
- **Auth Attempt**: Automatically uses the correct field type when authenticating

### 2. API Authentication Controllers Updated

#### Api/Auth/ForgotPasswordController.php
- **New Parameter**: Added `type` parameter option
- **Supported Values**: `email`, `username`, `phone`
- **Validation**: Phone numbers validated with regex and minimum length
- **Error Handling**: Specific error messages per field type

---

## How It Works

### Phone Number Detection (Web Only)

The system automatically detects input type:

```php
// These are automatically detected as phone numbers:
+1234567890
+1 (234) 567-8900
234-567-8900
(234) 567-8900
23456789012
+254 712 345 678

// These are NOT detected as phone numbers (treated as username):
user123
john_doe
test_user
```

### Login Flow

1. **User enters credential** (email, username, or phone)
2. **System detects type** automatically
3. **Query executes** on correct field
4. **User authenticated** if credentials valid

### Password Reset Flow (Web)

1. **User enters email/username/phone**
2. **System detects type** automatically
3. **User found** by entered value
4. **Reset code sent** to user email
5. **User verifies code** and resets password

### Password Reset Flow (API)

1. **User specifies type** in request (email, username, or phone)
2. **Value validated** according to type
3. **User found** by specified field
4. **Reset code sent** to user email
5. **User verifies code** and resets password

---

## API Usage Examples

### Web Login with Phone

```bash
# Login with phone number
POST /login
Input:
  username: +1234567890 (or 234567890, or (123) 456-7890, etc.)
  password: user_password

# System automatically detects it's a phone number and queries the 'mobile' field
```

### Web Password Reset with Phone

```bash
# Reset password with phone number
POST /forgot-password
Input:
  email: +1234567890 (field is named 'email' but accepts phone too)
  cf-turnstile-response: turnstile_token

# System automatically detects it's a phone number
# Error message: "User not found with this Phone Number."
```

### API Password Reset with Phone

```bash
POST /api/password/request
Content-Type: application/json

{
  "type": "phone",
  "value": "+1234567890"
}

Response:
{
  "code": 200,
  "status": "ok",
  "message": {
    "success": ["Password reset email sent successfully"]
  },
  "data": {
    "email": "user@example.com"
  }
}
```

### API Password Reset - All Types

```bash
# Reset by email
{
  "type": "email",
  "value": "user@example.com"
}

# Reset by username
{
  "type": "username",
  "value": "john_doe"
}

# Reset by phone
{
  "type": "phone",
  "value": "+1234567890"
}
```

---

## User Experience Flow

### Login Screen Example

User can now enter any of these in the login field:

```
Username field accepts:
  ✓ john_doe (username)
  ✓ john@example.com (email)
  ✓ +1234567890 (phone)
  ✓ 234-567-8900 (phone)
  ✓ (123) 456-7890 (phone)
```

### Error Messages (Improved)

**Before:**
```
Error: User not found.
```

**After:**
```
Error: User not found with this Phone Number.
Error: User not found with this Email.
Error: User not found with this Username.
```

---

## Phone Number Format Support

The system supports multiple phone number formats:

✓ International: `+1 234 567 8900`
✓ US Format: `(234) 567-8900`
✓ Dashes: `234-567-8900`
✓ Spaces: `234 567 8900`
✓ Plain digits: `2345678900`
✓ International prefix: `+1234567890`

**Minimum length:** 10 digits

---

## Database Requirements

The User model must have the following fields:

- `mobile` - Stores phone number (this field already exists)
- `email` - Stores email address (already exists)
- `username` - Stores username (already exists)

**No database migrations needed** - uses existing fields.

---

## Code Changes Summary

### ForgotPasswordController.php
- Detects phone numbers with regex: `/^[0-9+\-\s\(\)]+$/`
- Validates minimum 10 digits
- Updates error messages with field type labels

### LoginController.php
- Enhanced `findUsername()` method
- Detects phone numbers automatically
- Supports 3 field types: email, mobile, username

### Api/Auth/ForgotPasswordController.php
- Added `phone` as third option in type parameter
- Phone validation: regex + min length
- Improved error messages

---

## Testing

### Test Cases

#### Login with Phone
```bash
# Test 1: Valid phone login
Username: +1234567890
Password: correct_password
Result: ✓ Login successful

# Test 2: Invalid phone login
Username: +1234567890
Password: wrong_password
Result: ✓ Invalid credentials error

# Test 3: Phone number not registered
Username: +9999999999
Password: any_password
Result: ✓ Invalid credentials error
```

#### Password Reset with Phone (Web)
```bash
# Test 1: Reset with registered phone
Email field: +1234567890
Result: ✓ Reset code sent
Error message: None

# Test 2: Reset with unregistered phone
Email field: +9999999999
Result: ✓ Error message: "User not found with this Phone Number."

# Test 3: Invalid phone format (less than 10 digits)
Email field: 12345 (5 digits)
Result: ✓ Treated as username lookup (correct behavior)
```

#### Password Reset with Phone (API)
```bash
# Test 1: Reset by phone
POST /api/password/request
{
  "type": "phone",
  "value": "+1234567890"
}
Result: ✓ 200 OK with reset code sent

# Test 2: Reset by invalid phone format
POST /api/password/request
{
  "type": "phone",
  "value": "abc"
}
Result: ✓ 422 Error: "Phone number must contain valid phone characters"

# Test 3: Reset with phone too short
POST /api/password/request
{
  "type": "phone",
  "value": "12345"
}
Result: ✓ 422 Error: "Phone number must be at least 10 digits"
```

---

## Backwards Compatibility

✅ **Fully backward compatible:**
- Existing email logins work unchanged
- Existing username logins work unchanged
- API still accepts email/username via `type` parameter
- No breaking changes to existing functionality

---

## Security Considerations

1. **Phone Number Privacy**
   - Phone numbers are treated the same as usernames
   - Not publicly exposed
   - Only used for authentication lookups

2. **Rate Limiting**
   - Same rate limiting applies to phone logins
   - 5 attempts per minute
   - Progressive delays after threshold

3. **Input Validation**
   - Phone numbers validated with regex
   - Minimum 10 digits required
   - International format supported

4. **Password Reset**
   - Reset codes still sent to email (not SMS)
   - User must verify code before changing password
   - Same security flow as email/username reset

---

## Future Enhancements (Optional)

Possible improvements for future versions:

1. **SMS-based Password Reset**
   - Send reset codes via SMS to phone number
   - Requires SMS provider integration (Twilio, etc.)

2. **Phone Verification**
   - Verify phone numbers during signup
   - Two-factor authentication via SMS

3. **Phone Number Formatting**
   - Auto-format phone numbers on input
   - Country-specific validation

4. **Login Preferences**
   - Let users choose preferred login method
   - Remember last login method

---

## Troubleshooting

### Issue: Phone login not working

**Check:**
1. User has mobile field populated in database
2. Phone number has at least 10 digits
3. Phone number format contains only allowed characters

**Debug:**
```php
// Check what field type was detected
dd(request()->input('username')); // Shows detected field
```

### Issue: Password reset shows wrong error message

**Check:**
1. Error message should specify "Phone Number", not generic "User"
2. If showing "User not found", field detection is working
3. If phone not found, user may not have registered with that number

### Issue: API not accepting phone type

**Check:**
1. Verify `type` parameter is exactly "phone" (lowercase)
2. `value` should be phone number, not email
3. Phone should have at least 10 digits

---

## Support

For issues:

1. Check database has mobile field
2. Verify phone number format (10+ digits)
3. Review error messages for field type
4. Check authentication logs
5. Test with different login types (email, username) first

---

## Version Info

- **Updated**: April 2026
- **Laravel Version**: ^13.0
- **Requires**: Existing mobile field in users table
- **Backwards Compatible**: ✓ Yes

---

**Phone number authentication is now available for login and password reset!**
