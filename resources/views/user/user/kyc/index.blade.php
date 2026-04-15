@extends($activeTemplate.'layouts.dashboard')

@section('content')
    @if (! $hasActiveAccess)
        <section class="space-y-8">
            <div class="hero-surface p-6 sm:p-8">
                <div class="space-y-4">
                    <div class="flex flex-wrap gap-2">
                        <span class="section-kicker">Verification</span>
                        <span class="section-kicker">Services</span>
                    </div>

                    <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                        KYC & Verification Services
                    </h2>
                    <p class="max-w-3xl section-copy">
                        Upgrade with Paystack subscription billing to unlock the KYC services configured for your account plan.
                    </p>
                </div>
            </div>

            <div class="mx-6 rounded-2xl border border-amber-200 bg-amber-50 p-6 dark:border-amber-800/50 dark:bg-amber-900/30">
                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <div class="flex gap-3">
                        <svg class="mt-0.5 h-6 w-6 flex-shrink-0 text-amber-600 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2Z"></path>
                            <path d="M12 8v4"></path>
                            <path d="M12 16h.01"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-amber-900 dark:text-amber-200">Upgrade required</h3>
                            <p class="mt-1 text-sm text-amber-800 dark:text-amber-300">
                                KYC verification services are only available on an active subscription.
                            </p>
                        </div>
                    </div>
                    @if ($isFundingEligible)
                        <a href="{{ route('user.kyc.upgrade') }}" class="inline-flex h-10 items-center rounded-full bg-amber-600 px-6 text-sm font-semibold text-white transition hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600">
                            Upgrade Now
                        </a>
                    @else
                        <div class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                            Deposit at least ₦{{ number_format((float) $minimumFundingAmount, 2) }} first.
                        </div>
                    @endif
                </div>
            </div>

            @if (! $isFundingEligible)
                <div class="mx-6 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900 dark:border-sky-800/40 dark:bg-sky-900/20 dark:text-sky-100">
                    Successful deposits so far: <strong>₦{{ number_format((float) $fundedAmount, 2) }}</strong>. KYC access starts once your wallet funding reaches <strong>₦{{ number_format((float) $minimumFundingAmount, 2) }}</strong>.
                </div>
            @endif

            <section class="px-6">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($enabledPlans as $plan)
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-zinc-900/80">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-slate-950 dark:text-white">{{ $plan->name }}</h3>
                                @if ($plan->badge)
                                    <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">{{ $plan->badge }}</span>
                                @endif
                            </div>
                            <p class="mt-3 text-2xl font-bold text-sky-600 dark:text-sky-400">₦{{ number_format((float) $plan->price) }}/{{ $plan->paystack_interval }}</p>
                            <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">{{ $plan->description }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </section>
    @else
        <section class="space-y-8">
            <div class="hero-surface p-6 sm:p-8">
                <div class="space-y-4">
                    <div class="flex flex-wrap gap-2">
                        <span class="section-kicker">Verification</span>
                        <span class="section-kicker">Services</span>
                    </div>

                    <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                        Available KYC Services
                    </h2>

                    <div class="flex flex-wrap items-center gap-2">
                        @if ($activePlan)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                Plan: {{ $activePlan->name }}
                            </span>
                        @endif
                        @if ($user->kyc_subscription_status)
                            <span class="inline-flex items-center rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                                Status: {{ ucwords(str_replace('-', ' ', $user->kyc_subscription_status)) }}
                            </span>
                        @endif
                        <span class="text-xs text-slate-600 dark:text-zinc-400">
                            {{ $enabledServices->count() }} enabled services
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 px-6 lg:grid-cols-3">
                <div class="panel-card p-5">
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Billing cycle</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">
                        {{ $activePlan?->paystack_interval ? ucfirst($activePlan->paystack_interval) : 'Monthly' }}
                    </p>
                </div>
                <div class="panel-card p-5">
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Next payment</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">
                        {{ $user->kyc_subscription_next_payment_at ? $user->kyc_subscription_next_payment_at->format('d M Y') : 'Pending' }}
                    </p>
                </div>
                <div class="panel-card p-5">
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Monthly limit</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">
                        {{ (float) $user->kyc_monthly_limit >= 999999999 ? 'Unlimited' : '₦'.number_format((float) $user->kyc_monthly_limit) }}
                    </p>
                </div>
            </div>

            <div class="px-6">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($enabledServices as $service)
                        <div class="panel-card space-y-3 p-4">
                            <div class="space-y-1">
                                <h4 class="text-sm font-semibold text-slate-950 dark:text-white">{{ $service->name }}</h4>
                                <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $service->description }}</p>
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-200 pt-2 dark:border-white/10">
                                <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                                    {{ ucfirst($service->provider) }}
                                </span>
                                <span class="text-xs font-semibold text-slate-950 dark:text-white">₦{{ number_format((float) $service->price) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-3 px-6">
                @if ($canManageBilling)
                    <a href="{{ route('user.kyc.manage') }}" class="inline-flex h-11 items-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                        Manage Billing
                    </a>
                @endif
                <a href="{{ route('user.kyc.upgrade') }}" class="inline-flex h-11 items-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                    Change Plan
                </a>
                <a href="{{ route('user.home') }}" class="inline-flex h-11 items-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                    Back to Dashboard
                </a>
            </div>
        </section>
    @endif
@endsection
