@extends($activeTemplate.'layouts.dashboard')

@section('content')

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div>
            <div class="flex flex-wrap gap-2">
                <span class="section-kicker">Security</span>
                <span class="section-kicker">PIN Management</span>
            </div>

            <div class="space-y-4">
                <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Manage your PIN
                </h2>
                <p class="max-w-2xl section-copy">
                    Your 4-digit transaction PIN adds an extra layer of security to your account. Manage your PIN settings, enable/disable PIN protection, and configure authentication requirements.
                </p>
            </div>
        </div>
    </div>

    <!-- PIN Status Overview -->
    <section class="panel-card p-6">
        <div class="grid gap-6 sm:grid-cols-3">
            <!-- PIN Status -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 dark:border-white/10 dark:bg-white/5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">PIN Status</p>
                        <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">
                            @if($user->isPinEnabled())
                                <span class="text-emerald-600 dark:text-emerald-400">Active</span>
                            @else
                                <span class="text-amber-600 dark:text-amber-400">{{ $user->pin ? 'Inactive' : 'Not Set' }}</span>
                            @endif
                        </p>
                        <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">
                            @if($user->isPinEnabled())
                                PIN is protecting your transactions
                            @else
                                {{ $user->pin ? 'PIN is set but disabled' : 'Set a PIN to secure your account' }}
                            @endif
                        </p>
                    </div>
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl" style="background-color: {{ $user->isPinEnabled() ? 'rgba(16, 185, 129, 0.1)' : 'rgba(251, 146, 60, 0.1)' }}; color: {{ $user->isPinEnabled() ? '#10b981' : '#fb923c' }};">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- 2FA Status -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 dark:border-white/10 dark:bg-white/5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">2FA Status</p>
                        <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">
                            @if($user->isTwoFactorEnabled())
                                <span class="text-emerald-600 dark:text-emerald-400">Enabled</span>
                            @else
                                <span class="text-slate-400 dark:text-zinc-500">Disabled</span>
                            @endif
                        </p>
                        <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">
                            @if($user->isTwoFactorEnabled())
                                Authenticator is active
                            @else
                                Set up Google Authenticator
                            @endif
                        </p>
                    </div>
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Passkey Status -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 dark:border-white/10 dark:bg-white/5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Passkey Status</p>
                        <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">
                            @if($user->isPasskeyEnabled())
                                <span class="text-emerald-600 dark:text-emerald-400">Active</span>
                            @else
                                <span class="text-slate-400 dark:text-zinc-500">Not Set</span>
                            @endif
                        </p>
                        <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">
                            @if($user->isPasskeyEnabled())
                                {{ $user->passkeys()->count() }} key(s) registered
                            @else
                                Set up biometric/hardware keys
                            @endif
                        </p>
                    </div>
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PIN Management Actions -->
    @if(!$user->isPinEnabled() || !$user->pin)
        <!-- Setup New PIN -->
        <section class="panel-card p-6 border-l-4 border-l-sky-500">
            <div class="mb-6">
                <p class="section-kicker">Get Started</p>
                <h3 class="mt-3 section-title">Set up your PIN</h3>
            </div>
            <p class="mb-4 text-sm text-slate-600 dark:text-zinc-400">
                Create a 4-digit PIN to protect your transactions. This PIN will be required when making payments or purchases.
            </p>
            <a href="{{ route('user.user.pin.setup') }}" class="inline-flex h-11 w-full items-center justify-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                Create PIN
            </a>
        </section>
    @else
        <!-- Manage Existing PIN -->
        <section class="grid gap-6 sm:grid-cols-2">
            <!-- Change PIN -->
            <div class="panel-card p-6">
                <div class="mb-6">
                    <p class="section-kicker">Update</p>
                    <h3 class="mt-3 section-title">Change PIN</h3>
                </div>
                <p class="mb-4 text-sm text-slate-600 dark:text-zinc-400">
                    Update your current PIN with a new 4-digit code.
                </p>
                <a href="{{ route('user.user.pin.change') }}" class="inline-flex h-11 w-full items-center justify-center rounded-full border border-sky-300 bg-sky-50 px-6 text-sm font-semibold text-sky-700 transition hover:bg-sky-100 dark:border-sky-500/30 dark:bg-sky-900/20 dark:text-sky-300 dark:hover:bg-sky-900/30">
                    Change PIN
                </a>
            </div>

            <!-- Reset PIN -->
            <div class="panel-card p-6">
                <div class="mb-6">
                    <p class="section-kicker">Recovery</p>
                    <h3 class="mt-3 section-title">Reset PIN</h3>
                </div>
                <p class="mb-4 text-sm text-slate-600 dark:text-zinc-400">
                    Reset your PIN using your account password if you've forgotten it.
                </p>
                <a href="{{ route('user.user.pin.reset') }}" class="inline-flex h-11 w-full items-center justify-center rounded-full border border-amber-300 bg-amber-50 px-6 text-sm font-semibold text-amber-700 transition hover:bg-amber-100 dark:border-amber-500/30 dark:bg-amber-900/20 dark:text-amber-300 dark:hover:bg-amber-900/30">
                    Reset PIN
                </a>
            </div>

            <!-- Disable PIN -->
            @if($user->isTwoFactorEnabled())
                <div class="panel-card p-6">
                    <div class="mb-6">
                        <p class="section-kicker">Security</p>
                        <h3 class="mt-3 section-title">Disable PIN</h3>
                    </div>
                    <p class="mb-4 text-sm text-slate-600 dark:text-zinc-400">
                        Turn off PIN protection (requires 2FA to be enabled).
                    </p>
                    <a href="{{ route('user.user.pin.disable') }}" class="inline-flex h-11 w-full items-center justify-center rounded-full border border-rose-300 bg-rose-50 px-6 text-sm font-semibold text-rose-700 transition hover:bg-rose-100 dark:border-rose-500/30 dark:bg-rose-900/20 dark:text-rose-300 dark:hover:bg-rose-900/30">
                        Disable PIN
                    </a>
                </div>
            @endif
        </section>

        <!-- PIN Status Toggle -->
        <section class="panel-card p-6">
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="section-kicker">Control</p>
                        <h3 class="mt-3 section-title">Toggle PIN Protection</h3>
                    </div>
                    <div class="flex items-center gap-2 rounded-full px-4 py-2 bg-emerald-100 dark:bg-emerald-900/30">
                        <span class="h-2 w-2 rounded-full bg-emerald-600 dark:bg-emerald-400"></span>
                        <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Currently Active</span>
                    </div>
                </div>
            </div>

            <form method="post" action="{{ route('user.toggle.pin') }}" class="max-w-lg space-y-4">
                @csrf
                <p class="text-sm text-slate-600 dark:text-zinc-400">
                    Temporarily disable PIN without deleting it. You can enable it again anytime.
                </p>
                <button type="submit" class="app-submit-button inline-flex h-11 rounded-full bg-rose-600 px-6 text-sm font-semibold text-white transition hover:bg-rose-700 dark:bg-rose-500 dark:hover:bg-rose-600">
                    Temporarily Disable PIN
                </button>
            </form>
        </section>
    @endif

    <!-- Important Notes -->
    <section class="rounded-2xl border border-blue-200 bg-blue-50/50 p-6 dark:border-blue-500/30 dark:bg-blue-900/10">
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            </div>
            <div>
                <h4 class="font-semibold text-blue-950 dark:text-blue-50">Important Security Information</h4>
                <ul class="mt-2 space-y-1 text-sm text-blue-900 dark:text-blue-200">
                    <li>• If PIN is disabled, Two-Factor Authentication must be enabled for security</li>
                    <li>• Your PIN is never stored in plain text and cannot be retrieved</li>
                    <li>• Failed PIN attempts will temporarily lock your account</li>
                    <li>• Always keep your PIN confidential and never share it with anyone</li>
                </ul>
            </div>
        </div>
    </section>
</section>

@endsection
