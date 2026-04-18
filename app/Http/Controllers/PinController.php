<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PinController extends Controller
{
    const PIN_MAX_FAILED_ATTEMPTS = 5;
    const PIN_LOCKOUT_DURATION = 15; // minutes

    /**
     * Display PIN settings page
     */
    public function index()
    {
        $pageTitle = 'PIN Settings';
        $user = auth()->user();

        return view('user.user.pin.pin', compact(
            'pageTitle',
            'user'
        ));
    }

    /**
     * Show PIN setup view
     */
    public function showSetupForm()
    {
        $pageTitle = 'Set PIN';
        $user = auth()->user();

        if ($user->isPinEnabled()) {
            $notify[] = ['error', 'PIN is already set and enabled.'];
            return redirect()->route('user.pin.index')->withNotify($notify);
        }

        return view('user.user.pin.setup', compact('pageTitle', 'user'));
    }

    /**
     * Set a new PIN (initial setup)
     */
    public function setPin(Request $request)
    {
        $user = auth()->user();

        // If PIN is already set and enabled, deny
        if ($user->isPinEnabled()) {
            $notify[] = ['error', 'PIN is already set. Use Change PIN to modify it.'];
            return back()->withNotify($notify);
        }

        $this->validatePin($request, true);

        $pin = $request->input('pin');
        $verifyPin = $request->input('verify_pin');

        if ($pin !== $verifyPin) {
            $notify[] = ['error', 'PIN does not match verification PIN.'];
            return back()->withNotify($notify);
        }

        // Set the PIN
        $user->update([
            'pin' => $pin,
            'pin_state' => 1,
            'pin_enabled_at' => now(),
            'pin_failed_attempts' => 0,
        ]);

        $notify[] = ['success', 'PIN set successfully. Your account is now more secure.'];
        return redirect()->route('user.pin.index')->withNotify($notify);
    }

    /**
     * Show PIN change form
     */
    public function showChangeForm()
    {
        $pageTitle = 'Change PIN';
        $user = auth()->user();

        if (!$user->isPinEnabled()) {
            $notify[] = ['error', 'You must set a PIN first.'];
            return redirect()->route('user.pin.index')->withNotify($notify);
        }

        return view('user.user.pin.change', compact('pageTitle', 'user'));
    }

    /**
     * Change existing PIN
     */
    public function changePin(Request $request)
    {
        $user = auth()->user();

        if (!$user->isPinEnabled()) {
            $notify[] = ['error', 'PIN is not currently enabled.'];
            return back()->withNotify($notify);
        }

        if ($user->isPinLocked()) {
            $remainingTime = $user->pin_locked_until->diffInMinutes(now());
            $notify[] = ['error', "Too many failed attempts. Please try again in {$remainingTime} minutes."];
            return back()->withNotify($notify);
        }

        $this->validatePin($request);
        $this->validatePinChange($request, $user);

        $oldPin = $request->input('old_pin');
        $newPin = $request->input('pin');

        if ($oldPin !== $user->pin) {
            $user->increment('pin_failed_attempts');
            
            if ($user->pin_failed_attempts >= self::PIN_MAX_FAILED_ATTEMPTS) {
                $user->update(['pin_locked_until' => now()->addMinutes(self::PIN_LOCKOUT_DURATION)]);
                $notify[] = ['error', 'Too many failed attempts. PIN is locked for ' . self::PIN_LOCKOUT_DURATION . ' minutes.'];
            } else {
                $attempts = self::PIN_MAX_FAILED_ATTEMPTS - $user->pin_failed_attempts;
                $notify[] = ['error', "Incorrect PIN. You have {$attempts} attempt(s) remaining."];
            }
            
            return back()->withNotify($notify);
        }

        // Reset failed attempts and update PIN
        $user->update([
            'pin' => $newPin,
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null,
        ]);

        $notify[] = ['success', 'PIN changed successfully.'];
        return redirect()->route('user.pin.index')->withNotify($notify);
    }

    /**
     * Show PIN reset form
     */
    public function showResetForm()
    {
        $pageTitle = 'Reset PIN';
        
        return view('user.user.pin.reset', compact('pageTitle'));
    }

    /**
     * Reset PIN (requires password verification)
     */
    public function resetPin(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'password' => 'required|string',
            'pin' => 'required|string|min:4|max:4|not_in:0000,1111,2222,3333,4444,5555,6666,7777,8888,9999',
            'verify_pin' => 'required|string|min:4|max:4',
        ]);

        // Verify password
        if (!Hash::check($request->input('password'), $user->password)) {
            $notify[] = ['error', 'Incorrect password.'];
            return back()->withNotify($notify);
        }

        $pin = $request->input('pin');
        $verifyPin = $request->input('verify_pin');

        if ($pin !== $verifyPin) {
            $notify[] = ['error', 'PIN does not match verification PIN.'];
            return back()->withNotify($notify);
        }

        // Reset the PIN
        $user->update([
            'pin' => $pin,
            'pin_state' => 1,
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null,
        ]);

        $notify[] = ['success', 'PIN has been reset successfully.'];
        return redirect()->route('user.pin.index')->withNotify($notify);
    }

    /**
     * Show PIN disable form
     */
    public function showDisableForm()
    {
        $pageTitle = 'Disable PIN';
        $user = auth()->user();

        if (!$user->isPinEnabled()) {
            $notify[] = ['error', 'PIN is not currently enabled.'];
            return redirect()->route('user.pin.index')->withNotify($notify);
        }

        return view('user.user.pin.disable', compact('pageTitle', 'user'));
    }

    /**
     * Disable PIN (requires verification)
     */
    public function disablePin(Request $request)
    {
        $user = auth()->user();

        if (!$user->isPinEnabled()) {
            $notify[] = ['error', 'PIN is not currently enabled.'];
            return back()->withNotify($notify);
        }

        // Check if 2FA is enabled - PIN cannot be disabled if 2FA is off
        if (!$user->isTwoFactorEnabled()) {
            $notify[] = ['error', 'You cannot disable PIN unless Two-Factor Authentication is enabled.'];
            return back()->withNotify($notify);
        }

        $this->validate($request, [
            'pin' => 'required|string|min:4|max:4',
        ]);

        $enteredPin = $request->input('pin');

        if ($enteredPin !== $user->pin) {
            $notify[] = ['error', 'Incorrect PIN.'];
            return back()->withNotify($notify);
        }

        // Disable PIN
        $user->update([
            'pin_state' => 0,
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null,
        ]);

        $notify[] = ['success', 'PIN has been disabled. Your account is now secured with Two-Factor Authentication.'];
        return redirect()->route('user.pin.index')->withNotify($notify);
    }

    /**
     * Validate PIN format
     */
    protected function validatePin(Request $request, $isNew = false)
    {
        $rules = [
            'pin' => 'required|string|min:4|max:4|regex:/^\d{4}$/|not_in:0000,1111,2222,3333,4444,5555,6666,7777,8888,9999',
            'verify_pin' => 'required|string|min:4|max:4|regex:/^\d{4}$/',
        ];

        $messages = [
            'pin.regex' => 'PIN must be 4 digits.',
            'pin.not_in' => 'PIN cannot be all same digits (0000, 1111, etc.).',
            'verify_pin.regex' => 'Verification PIN must be 4 digits.',
        ];

        $this->validate($request, $rules, $messages);
    }

    /**
     * Validate PIN change requirements
     */
    protected function validatePinChange(Request $request, User $user)
    {
        $newPin = $request->input('pin');
        $oldPin = $user->pin;

        if ($newPin === $oldPin) {
            throw new \Illuminate\Validation\ValidationException(
                \Illuminate\Validation\Validator::make(
                    [],
                    []
                )->errors()->add('pin', 'New PIN cannot be the same as old PIN.')
            );
        }
    }

    /**
     * Toggle PIN state via API
     */
    public function togglePin(Request $request)
    {
        $user = auth()->user();

        if (!$user->isPinEnabled()) {
            // Cannot toggle on if PIN not set
            $notify[] = ['error', 'PIN must be set first.'];
            return back()->withNotify($notify);
        }

        // Toggle state
        $newState = $user->pin_state == 1 ? 0 : 1;

        // If disabling, check 2FA requirement
        if ($newState == 0 && !$user->isTwoFactorEnabled()) {
            $notify[] = ['error', 'Cannot disable PIN without enabling 2FA.'];
            return back()->withNotify($notify);
        }

        $user->update(['pin_state' => $newState]);

        $message = $newState == 1 ? 'PIN enabled successfully.' : 'PIN disabled successfully.';
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }
}