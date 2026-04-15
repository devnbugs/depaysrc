<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class SecuritySettingsController extends Controller
{
    /**
     * Display unified security settings page
     */
    public function index()
    {
        $pageTitle = 'Security Settings';
        $user = Auth::user()->load('passkeys', 'login_logs');

        return view('user.user.settings.security-settings', compact(
            'pageTitle',
            'user'
        ));
    }
}
