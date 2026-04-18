@extends($activeTemplate.'layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="hero-surface p-6 sm:p-8 rounded-3xl">
        <div class="space-y-4">
            <div class="flex flex-wrap gap-2">
                <span class="section-kicker">Account</span>
                <span class="section-kicker">Security</span>
            </div>

            <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                Security Settings
            </h2>
            <p class="max-w-3xl section-copy">
                Manage your account security with passkeys, two-factor authentication, and transaction PIN. Choose the authentication methods that work best for you.
            </p>
        </div>
    </div>

    <!-- Security Status Cards -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Passkeys Status -->
        <article class="panel-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Passkeys</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-white">{{ auth()->user()->passkeys->count() }}</p>
                </div>
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-sky-100 text-sky-600 dark:bg-sky-500/20 dark:text-sky-300">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="13" width="18" height="8" rx="2"></rect>
                        <path d="M7 19c2 2 4 2 10 2"></path>
                        <path d="M7 13V8a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v5"></path>
                    </svg>
                </span>
            </div>
        </article>

        <!-- 2FA Status -->
        <article class="panel-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">2FA Status</p>
                    <p class="mt-3 text-lg font-semibold text-slate-950 dark:text-white">
                        @if(auth()->user()->isTwoFactorEnabled())
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-600 dark:bg-emerald-300"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 dark:bg-slate-500/20 dark:text-slate-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-600 dark:bg-slate-300"></span>
                                Inactive
                            </span>
                        @endif
                    </p>
                </div>
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full @if(auth()->user()->isTwoFactorEnabled()) bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300 @else bg-slate-100 text-slate-600 dark:bg-slate-500/20 dark:text-slate-300 @endif">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </span>
            </div>
        </article>

        <!-- PIN Status -->
        <article class="panel-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">PIN Status</p>
                    <p class="mt-3 text-lg font-semibold text-slate-950 dark:text-white">
                        @if(auth()->user()->isPinEnabled())
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-600 dark:bg-emerald-300"></span>
                                Set
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-yellow-600 dark:bg-yellow-300"></span>
                                Not Set
                            </span>
                        @endif
                    </p>
                </div>
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full @if(auth()->user()->isPinEnabled()) bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300 @else bg-yellow-100 text-yellow-600 dark:bg-yellow-500/20 dark:text-yellow-300 @endif">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="19" cy="12" r="1"></circle>
                        <circle cx="5" cy="12" r="1"></circle>
                    </svg>
                </span>
            </div>
        </article>

        <!-- Last Login -->
        <article class="panel-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Last Login</p>
                    <p class="mt-3 text-sm font-semibold text-slate-950 dark:text-white">
                        @php
                            $lastLogin = auth()->user()->login_logs()->latest()->first();
                        @endphp
                        @if($lastLogin)
                            {{ $lastLogin->created_at->format('M d, Y') }}
                        @else
                            Today
                        @endif
                    </p>
                </div>
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-500/20 dark:text-purple-300">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </span>
            </div>
        </article>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-slate-200 dark:border-white/10">
        <nav class="flex gap-8" role="tablist">
            <button
                role="tab"
                aria-selected="true"
                data-tab="passkeys"
                class="tab-button active py-3 text-sm font-semibold text-slate-900 transition border-b-2 border-sky-600 dark:text-white dark:border-sky-500"
            >
                Passkeys
            </button>
            <button
                role="tab"
                aria-selected="false"
                data-tab="2fa"
                class="tab-button py-3 text-sm font-semibold text-slate-500 transition border-b-2 border-transparent hover:text-slate-900 dark:text-zinc-400 dark:hover:text-white dark:border-transparent"
            >
                Two-Factor Authentication
            </button>
            <button
                role="tab"
                aria-selected="false"
                data-tab="pin"
                class="tab-button py-3 text-sm font-semibold text-slate-500 transition border-b-2 border-transparent hover:text-slate-900 dark:text-zinc-400 dark:hover:text-white dark:border-transparent"
            >
                Transaction PIN
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="space-y-6">
        <!-- Passkeys Tab -->
        <div id="passkeys-tab" class="tab-content">
            <section class="space-y-4">
                <livewire:passkey-manager />
            </section>
        </div>

        <!-- 2FA Tab -->
        <div id="2fa-tab" class="tab-content hidden">
            <section class="space-y-4">
                <livewire:two-factor-authenticator />
            </section>
        </div>

        <!-- PIN Tab -->
        <div id="pin-tab" class="tab-content hidden">
            <div class="space-y-6">
                @if(auth()->user()->isPinEnabled())
                    <!-- PIN is Set -->
                    <div class="rounded-3xl border border-emerald-200 bg-emerald-50/60 p-6 dark:border-emerald-500/30 dark:bg-emerald-500/10">
                        <div class="flex items-start gap-4">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/20">
                                <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-emerald-950 dark:text-emerald-50">Transaction PIN Active</h4>
                                <p class="mt-1 text-sm leading-6 text-emerald-900 dark:text-emerald-200">
                                    Your transaction PIN is set and active. Use it to authorize sensitive transactions on your account.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- PIN Actions -->
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Change PIN -->
                        <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5 dark:border-white/10 dark:bg-white/5">
                            <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Change PIN</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                Update your current transaction PIN to a new one.
                            </p>
                            <a href="{{ route('user.user.pin.change') }}" class="mt-4 inline-flex h-11 w-full items-center justify-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                                Change PIN
                            </a>
                        </div>

                        <!-- Reset PIN -->
                        <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5 dark:border-white/10 dark:bg-white/5">
                            <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Reset PIN</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                                If you've forgotten your PIN, you can reset it here.
                            </p>
                            <a href="{{ route('user.user.pin.reset') }}" class="mt-4 inline-flex h-11 w-full items-center justify-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-200 dark:hover:bg-white/5">
                                Reset PIN
                            </a>
                        </div>
                    </div>

                    <!-- Disable PIN -->
                    <div class="rounded-3xl border border-rose-200 bg-rose-50/60 p-5 dark:border-rose-500/30 dark:bg-rose-500/10">
                        <h3 class="text-lg font-semibold text-rose-950 dark:text-rose-50">Disable PIN</h3>
                        <p class="mt-2 text-sm leading-6 text-rose-900 dark:text-rose-200">
                            Remove transaction PIN protection from your account. You'll need to enable PIN or 2FA before removing this.
                        </p>
                        <a href="{{ route('useruser.pin.disable') }}" class="mt-4 inline-flex h-11 w-full items-center justify-center rounded-full border border-rose-200 bg-white px-6 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-500/30 dark:bg-zinc-950 dark:text-rose-300 dark:hover:bg-rose-500/10">
                            Disable PIN
                        </a>
                    </div>
                @else
                    <!-- PIN Not Set -->
                    <div class="rounded-3xl border border-yellow-200 bg-yellow-50/60 p-6 dark:border-yellow-500/30 dark:bg-yellow-500/10">
                        <div class="flex items-start gap-4">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-500/20">
                                <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-yellow-950 dark:text-yellow-50">Transaction PIN Not Set</h4>
                                <p class="mt-1 text-sm leading-6 text-yellow-900 dark:text-yellow-200">
                                    Set up a transaction PIN to add an extra layer of security to your account. This PIN will be required to authorize sensitive transactions.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Setup PIN -->
                    <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5 dark:border-white/10 dark:bg-white/5">
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Setup Transaction PIN</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                            Create a 4-6 digit PIN that you'll use to authorize transactions on your account.
                        </p>
                        <a href="{{ route('user.user.pin.setup') }}" class="mt-4 inline-flex h-11 w-full items-center justify-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                            Setup PIN
                        </a>
                    </div>
                @endif

                <!-- PIN Info Box -->
                <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-6 dark:border-blue-500/30 dark:bg-blue-900/10">
                    <h4 class="font-semibold text-blue-950 dark:text-blue-50">About Transaction PIN</h4>
                    <ul class="mt-3 space-y-2 text-sm leading-6 text-blue-900 dark:text-blue-200">
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-blue-600 dark:bg-blue-300"></span>
                            <span>Your PIN is a 4-6 digit code known only to you</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-blue-600 dark:bg-blue-300"></span>
                            <span>It's used to authorize withdrawals, transfers, and other sensitive actions</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-blue-600 dark:bg-blue-300"></span>
                            <span>Never share your PIN with anyone, even support staff</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-blue-600 dark:bg-blue-300"></span>
                            <span>If you forget your PIN, you can reset it through email verification</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Recommendations -->
    <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-6 dark:border-white/10 dark:bg-white/5">
        <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Security Recommendations</h3>
        <div class="mt-4 space-y-3">
            <div class="flex items-start gap-3">
                <span class="inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-600 dark:bg-sky-500/20 dark:text-sky-300">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
                <span class="text-sm text-slate-700 dark:text-zinc-300">
                    <strong>Enable Multiple Authentication Methods:</strong> Use both passkeys and 2FA for maximum security
                </span>
            </div>
            <div class="flex items-start gap-3">
                <span class="inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-600 dark:bg-sky-500/20 dark:text-sky-300">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
                <span class="text-sm text-slate-700 dark:text-zinc-300">
                    <strong>Use A Strong PIN:</strong> Choose a PIN that's memorable but not easy to guess
                </span>
            </div>
            <div class="flex items-start gap-3">
                <span class="inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-600 dark:bg-sky-500/20 dark:text-sky-300">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
                <span class="text-sm text-slate-700 dark:text-zinc-300">
                    <strong>Backup Your Codes:</strong> Save your 2FA backup codes in a secure location
                </span>
            </div>
            <div class="flex items-start gap-3">
                <span class="inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-600 dark:bg-sky-500/20 dark:text-sky-300">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
                <span class="text-sm text-slate-700 dark:text-zinc-300">
                    <strong>Monitor Active Sessions:</strong> Review your login history and activity regularly
                </span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');

                // Remove active state from all buttons and contents
                tabButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-selected', 'false');
                    btn.classList.remove('border-sky-600', 'text-slate-900', 'dark:border-sky-500', 'dark:text-white');
                    btn.classList.add('border-transparent', 'text-slate-500', 'dark:text-zinc-400');
                });
                tabContents.forEach(content => content.classList.add('hidden'));

                // Add active state to clicked button and corresponding content
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                this.classList.remove('border-transparent', 'text-slate-500', 'dark:text-zinc-400');
                this.classList.add('border-sky-600', 'text-slate-900', 'dark:border-sky-500', 'dark:text-white');

                const tabContent = document.getElementById(tabName + '-tab');
                if (tabContent) {
                    tabContent.classList.remove('hidden');
                }
            });
        });
    });
</script>
@endsection
