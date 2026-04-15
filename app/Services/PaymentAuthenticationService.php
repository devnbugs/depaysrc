<?php

namespace App\Services;

use App\Models\User;
use App\Models\AuthenticationVerification;
use Illuminate\Support\Facades\Auth;

class PaymentAuthenticationService
{
    /**
     * Check if user needs authentication for payment
     */
    public static function requiresAuthentication(User $user, string $context = 'payment'): bool
    {
        // User must have at least one authentication method enabled
        if (!$user->isPinEnabled() && !$user->isTwoFactorEnabled() && !$user->isPasskeyEnabled()) {
            return false;
        }

        return true;
    }

    /**
     * Determine which authentication method to use for payment
     */
    public static function getRequiredAuthMethod(User $user): array
    {
        $methods = [];

        // Rule: If PIN is enabled, it's checked first
        if ($user->isPinEnabled()) {
            $methods['pin'] = true;
        }

        // Rule: If 2FA is enabled and PIN is disabled, use 2FA
        if ($user->isTwoFactorEnabled() && !$user->isPinEnabled()) {
            $methods['two_factor'] = true;
        }

        // Passkey can be used if enabled
        if ($user->isPasskeyEnabled()) {
            $methods['passkey'] = true;
        }

        // Rule: If PIN is disabled, 2FA MUST be enabled
        if (!$user->isPinEnabled() && !$user->isTwoFactorEnabled()) {
            throw new \Exception('Account security issue: Neither PIN nor 2FA is enabled');
        }

        return $methods;
    }

    /**
     * Create authentication verification record
     */
    public static function createVerification(
        string $type,
        string $context,
        ?string $referenceId = null
    ): AuthenticationVerification {
        return AuthenticationVerification::createVerification(
            Auth::id(),
            $type,
            $context,
            $referenceId
        );
    }

    /**
     * Verify PIN for payment
     */
    public static function verifyPin(User $user, string $pin): bool
    {
        if (!$user->isPinEnabled()) {
            return false;
        }

        if ($user->isPinLocked()) {
            return false;
        }

        if ($pin !== $user->pin) {
            // Increment failed attempts
            $user->increment('pin_failed_attempts');
            
            // Lock if too many attempts
            if ($user->pin_failed_attempts >= 5) {
                $user->update(['pin_locked_until' => now()->addMinutes(15)]);
            }
            
            return false;
        }

        // Reset failed attempts on success
        $user->update([
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null
        ]);

        return true;
    }

    /**
     * Verify 2FA code for payment
     */
    public static function verify2FA(User $user, string $code): bool
    {
        if (!$user->isTwoFactorEnabled()) {
            return false;
        }

        // Use the helper function to verify
        return self::verifyG2fa($user, $code);
    }

    /**
     * Verify Google Authenticator code
     */
    public static function verifyG2fa($user, $code): bool
    {
        if (!$user->two_factor_enabled || !$user->two_factor_secret) {
            return false;
        }

        try {
            $authenticator = new \Zend\Math\Rand();
            $secret = $user->two_factor_secret;
            $currentTime = floor(time() / 30);
            
            // Allow code from current time and ±1 time window for clock skew
            for ($i = -1; $i <= 1; $i++) {
                $expectedCode = self::generateGoogleAuthenticatorCode($secret, $currentTime + $i);
                if ($code === $expectedCode) {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            \Log::error('2FA verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate Google Authenticator code
     */
    private static function generateGoogleAuthenticatorCode($secret, $time): string
    {
        $secretKey = self::base32Decode($secret);
        $message = pack('N*', 0) . pack('J', $time);
        $hash = hash_hmac('sha1', $message, $secretKey, true);
        $offset = ord(substr($hash, -1)) & 0x0f;
        $code = (((ord(substr($hash, $offset, 1)) & 0x7f) << 24) |
            ((ord(substr($hash, $offset + 1, 1)) & 0xff) << 16) |
            ((ord(substr($hash, $offset + 2, 1)) & 0xff) << 8) |
            (ord(substr($hash, $offset + 3, 1)) & 0xff)) % 1000000;
        
        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Base32 decode
     */
    private static function base32Decode($input): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $input = strtoupper($input);
        $bits = '';
        $value = 0;

        for ($i = 0; $i < strlen($input); $i++) {
            $value = strpos($alphabet, $input[$i]);
            $bits .= sprintf('%05b', $value);
        }

        $chunks = str_split($bits, 8);
        $output = '';

        foreach ($chunks as $chunk) {
            if (strlen($chunk) == 8) {
                $output .= chr(bindec($chunk));
            }
        }

        return $output;
    }

    /**
     * Verify passkey for payment
     */
    public static function verifyPasskey(User $user, string $credentialId): bool
    {
        if (!$user->isPasskeyEnabled()) {
            return false;
        }

        // Find passkey
        $passkey = $user->passkeys()->where('credential_id', $credentialId)->first();
        
        if (!$passkey) {
            return false;
        }

        // Update last used
        $passkey->forceFill(['last_used_at' => now()])->save();

        return true;
    }

    /**
     * Check if payment/purchase exempts PIN requirement
     * Rule: If 2FA Enabled, PIN is Not Required at Payment
     */
    public static function isPinRequiredForPayment(User $user): bool
    {
        // If 2FA is enabled, PIN is not required
        if ($user->isTwoFactorEnabled()) {
            return false;
        }

        // Otherwise, if PIN is enabled, it's required
        return $user->isPinEnabled();
    }
}
