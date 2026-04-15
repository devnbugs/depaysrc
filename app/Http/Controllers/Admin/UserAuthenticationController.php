<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuthenticationVerification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserAuthenticationController extends Controller
{
    /**
     * Display authentication statistics
     */
    public function stats(): View
    {
        $totalUsers = User::count();
        
        $stats = [
            'total_users' => $totalUsers,
            'pin_enabled' => User::whereNotNull('pin_enabled_at')->count(),
            'pin_disabled' => User::where('pin_enabled', false)->whereNotNull('pin_enabled_at')->count(),
            'pin_not_set' => User::whereNull('pin_enabled_at')->count(),
            '2fa_enabled' => User::where('two_factor_enabled', true)->count(),
            'passkey_enabled' => User::whereHas('passkeys')->count(),
        ];

        // Calculate percentages
        $stats['pin_percentage'] = $totalUsers > 0 ? round(($stats['pin_enabled'] / $totalUsers) * 100, 1) : 0;
        $stats['2fa_percentage'] = $totalUsers > 0 ? round(($stats['2fa_enabled'] / $totalUsers) * 100, 1) : 0;
        $stats['passkey_percentage'] = $totalUsers > 0 ? round(($stats['passkey_enabled'] / $totalUsers) * 100, 1) : 0;

        // Verification attempts by type in last 7 days
        $verifications = AuthenticationVerification::where('created_at', '>=', now()->subDays(7))
            ->groupBy('type')
            ->selectRaw('type, COUNT(*) as count, SUM(CASE WHEN status = "verified" THEN 1 ELSE 0 END) as successful')
            ->get();

        return view('admin.users.authentication.stats', compact('stats', 'verifications'));
    }

    /**
     * View user authentication methods
     */
    public function showUser(User $user): View
    {
        // Load passkeys count
        $passkeysCount = $user->passkeys()->count();
        $user->passkeys_count = $passkeysCount;

        $verifications = AuthenticationVerification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.authentication.user', compact('user', 'verifications'));
    }

    /**
     * Reset user PIN
     */
    public function resetPin(Request $request, User $user)
    {
        $user->update([
            'pin_enabled' => true,
            'pin_enabled_at' => now(),
            'pin_failed_attempts' => 0,
            'pin_locked_until' => null,
        ]);

        return back()->with('success', 'PIN reset successfully for ' . $user->name);
    }

    /**
     * Disable user PIN
     */
    public function disablePin(User $user)
    {
        $user->update(['pin_enabled' => false]);
        return back()->with('success', 'PIN disabled for user');
    }

    /**
     * Unlock user PIN
     */
    public function unlockPin(User $user)
    {
        $user->update([
            'pin_locked_until' => null,
            'pin_failed_attempts' => 0,
        ]);

        return back()->with('success', 'PIN unlocked');
    }

    /**
     * Disable user 2FA
     */
    public function disable2fa(User $user)
    {
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
        ]);

        return back()->with('success', '2FA disabled for user');
    }

    /**
     * Disable user passkeys
     */
    public function disablePasskeys(User $user)
    {
        // Delete all passkey records
        $user->passkeys()->delete();

        // Update the user flag
        $user->update([
            'passkey_enabled' => false,
            'passkey_credentials' => null,
        ]);

        return back()->with('success', 'All passkeys disabled for user');
    }

    /**
     * View verification logs
     */
    public function logs(Request $request): View
    {
        $query = AuthenticationVerification::query();
        $dateRange = $request->input('date_range', '7');

        // Apply date range filter first
        if ($dateRange !== 'all') {
            $query->where('created_at', '>=', now()->subDays((int)$dateRange));
        }

        // Calculate summary before filtering
        $summaryQuery = clone $query;
        $summary = [
            'total' => $summaryQuery->count(),
            'verified' => (clone $summaryQuery)->where('status', 'verified')->count(),
            'failed' => (clone $summaryQuery)->where('status', 'failed')->count(),
            'pending' => (clone $summaryQuery)->where('status', 'pending')->count(),
        ];

        // Filter by type
        if ($request->has('type') && $request->input('type') !== '') {
            $query->where('type', $request->input('type'));
        }

        // Filter by status
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        // Filter by context
        if ($request->has('context') && $request->input('context') !== '') {
            $query->where('context', $request->input('context'));
        }

        $logs = $query->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.users.authentication.logs', compact('logs', 'summary'));
    }
}
