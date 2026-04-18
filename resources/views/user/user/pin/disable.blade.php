@extends($activeTemplate.'layouts.dashboard')

@section('content')

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div>
            <div class="flex flex-wrap gap-2">
                <span class="section-kicker">Security</span>
                <span class="section-kicker">PIN Disable</span>
            </div>

            <div class="space-y-4">
                <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Disable your PIN
                </h2>
                <p class="max-w-2xl section-copy">
                    You can temporarily disable your PIN protection. This requires Two-Factor Authentication to be enabled for account security.
                </p>
            </div>
        </div>
    </div>

    <section class="panel-card p-6 max-w-2xl">
        <div class="mb-6 rounded-2xl border border-orange-200 bg-orange-50/50 p-6 dark:border-orange-500/30 dark:bg-orange-900/10">
            <h4 class="font-semibold text-orange-950 dark:text-orange-50 mb-2">Important Security Notice</h4>
            <p class="text-sm text-orange-900 dark:text-orange-200">
                PIN cannot be disabled unless Two-Factor Authentication (2FA) is enabled. This ensures your account remains secure.
            </p>
        </div>

        @if(!$user->isTwoFactorEnabled())
            <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-6 dark:border-rose-500/30 dark:bg-rose-900/10">
                <div class="flex items-start gap-4">
                    <svg class="h-6 w-6 text-rose-600 dark:text-rose-400 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-rose-950 dark:text-rose-50">2FA Not Enabled</h4>
                        <p class="mt-2 text-sm text-rose-900 dark:text-rose-200">
                            You must enable Two-Factor Authentication before disabling your PIN. This is a required security measure to protect your account.
                        </p>
                        <a href="{{ route('user.twofactor') }}" class="mt-3 inline-flex h-10 items-center rounded-full bg-rose-600 px-4 text-sm font-semibold text-white transition hover:bg-rose-700 dark:bg-rose-500 dark:hover:bg-rose-600">
                            Enable 2FA Now
                        </a>
                    </div>
                </div>
            </div>
        @else
            <form action="{{ route('pin.disable') }}" method="post" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="pin" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                        Verify Current PIN <span class="text-rose-600">*</span>
                    </label>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mb-2">Enter your PIN to confirm this action</p>
                    <input 
                        type="password"
                        name="pin"
                        id="pin"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500 @error('pin') border-rose-500 dark:border-rose-500 @enderror"
                        placeholder="Enter your 4-digit PIN"
                        maxlength="4"
                        inputmode="numeric"
                        pattern="[0-9]{4}"
                        required
                    />
                    @error('pin')
                        <p class="text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-2xl border border-blue-200 bg-blue-50/50 p-6 dark:border-blue-500/30 dark:bg-blue-900/10">
                    <h4 class="font-semibold text-blue-950 dark:text-blue-50 mb-3">What happens after disabling?</h4>
                    <ul class="space-y-2 text-sm text-blue-900 dark:text-blue-200">
                        <li class="flex items-start gap-2">
                            <svg class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>PIN protection will be turned off for payments</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>2FA will remain active for login security</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>You can re-enable PIN anytime from settings</span>
                        </li>
                    </ul>
                </div>

                <div class="app-action-row border-t border-slate-200 pt-6 dark:border-white/10">
                    <button type="submit" class="app-submit-button inline-flex h-11 rounded-full bg-rose-600 px-6 text-sm font-semibold text-white transition hover:bg-rose-700 dark:bg-rose-500 dark:hover:bg-rose-600">
                        Disable PIN
                    </button>
                    <a href="{{ route('user.pin.index') }}" class="inline-flex h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                        Cancel
                    </a>
                </div>
            </form>
        @endif
    </section>
</section>

@endsection
