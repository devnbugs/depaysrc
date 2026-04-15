@extends($activeTemplate.'layouts.frontend')

@section('content')
@php
    $isLoggedIn = auth()->check();

    $primaryAction = $isLoggedIn
        ? ['label' => 'Open dashboard', 'route' => route('user.home')]
        : ['label' => 'Create account', 'route' => route('user.register')];

    $secondaryAction = $isLoggedIn
        ? ['label' => 'Fund wallet', 'route' => route('user.deposit')]
        : ['label' => 'Sign in', 'route' => route('user.login')];

    $channels = [
        ['title' => 'Airtime and data', 'copy' => 'Execute daily top-ups with simpler inputs, cleaner confirmations, and responsive purchase flows.', 'tone' => 'sky', 'icon' => 'wifi'],
        ['title' => 'Electricity and TV', 'copy' => 'Handle household billing with clear service validation and easier status tracking.', 'tone' => 'emerald', 'icon' => 'bolt'],
        ['title' => 'Wallet funding', 'copy' => 'Keep balances ready with bank funding channels and dashboard-level control over deposits.', 'tone' => 'violet', 'icon' => 'wallet'],
        ['title' => 'Transfers and controls', 'copy' => 'Move funds and review account protections through a more deliberate user experience.', 'tone' => 'slate', 'icon' => 'send'],
    ];

    $operatingPillars = [
        ['title' => 'Structured checkout', 'copy' => 'Each payment path is arranged to reduce friction without hiding the important checks.'],
        ['title' => 'Account protection', 'copy' => 'PIN, 2FA, passkeys, and verification layers are positioned closer to where risk actually happens.'],
        ['title' => 'Operational visibility', 'copy' => 'Recent activity, funding references, and billing history stay easier to scan across devices.'],
    ];

    $workflow = [
        ['step' => '01', 'title' => 'Create and verify', 'copy' => 'Open an account, confirm access, and complete the identity steps needed for deeper services.'],
        ['step' => '02', 'title' => 'Fund and prepare', 'copy' => 'Top up your wallet or use assigned funding accounts so routine transactions stay ready.'],
        ['step' => '03', 'title' => 'Pay, transfer, and track', 'copy' => 'Execute purchases from the dashboard and keep a cleaner record of every action taken.'],
    ];

    $partnerLogos = [
        ['src' => '/assets/frontend/assets/images/mtn.png', 'alt' => 'MTN'],
        ['src' => '/assets/frontend/assets/images/airtel.png', 'alt' => 'Airtel'],
        ['src' => '/assets/frontend/assets/images/glo.png', 'alt' => 'Glo'],
        ['src' => '/assets/frontend/assets/images/9mobile.png', 'alt' => '9mobile'],
        ['src' => '/assets/frontend/assets/images/eass.png', 'alt' => 'EASS'],
        ['src' => '/assets/frontend/assets/images/monnify.png', 'alt' => 'Monnify'],
    ];
@endphp

<section class="space-y-8">
    <section class="hero-surface overflow-hidden p-6 sm:p-8 lg:p-10">
        <div class="grid gap-8 xl:grid-cols-[1.08fr_0.92fr]">
            <div class="space-y-6">
                <div class="flex flex-wrap gap-2">
                    <span class="section-kicker">Payment operations</span>
                    <span class="section-kicker">Bills and wallet platform</span>
                </div>

                <div class="space-y-4">
                    <h1 class="max-w-4xl text-4xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-5xl lg:text-6xl">
                        A more serious platform for airtime, data, transfers, and recurring bill activity.
                    </h1>
                    <p class="max-w-3xl text-base leading-7 text-slate-600 dark:text-zinc-300 sm:text-lg">
                        {{ $general->sitename }} is built for users who want cleaner billing flows, stronger account controls, and a dashboard that stays readable on mobile phones, tablets, and larger workstations.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ $primaryAction['route'] }}" class="inline-flex h-12 items-center rounded-full bg-slate-950 px-6 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                        {{ $primaryAction['label'] }}
                    </a>
                    <a href="{{ $secondaryAction['route'] }}" class="inline-flex h-12 items-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                        {{ $secondaryAction['label'] }}
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex h-12 items-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                        Contact team
                    </a>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl border border-slate-200 bg-white/90 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Focus</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950 dark:text-white">Everyday payments</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white/90 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Security</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950 dark:text-white">Layered controls</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white/90 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Experience</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950 dark:text-white">Responsive by default</p>
                    </div>
                </div>
            </div>

            <div class="panel-card relative overflow-hidden p-6 sm:p-7">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(14,165,233,0.16),transparent_42%),radial-gradient(circle_at_bottom_left,rgba(15,23,42,0.08),transparent_36%)] dark:bg-[radial-gradient(circle_at_top_right,rgba(56,189,248,0.14),transparent_42%),radial-gradient(circle_at_bottom_left,rgba(148,163,184,0.08),transparent_34%)]"></div>
                <div class="relative space-y-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="section-kicker">Platform snapshot</p>
                            <h2 class="mt-3 text-2xl font-semibold tracking-tight text-slate-950 dark:text-white">
                                Built around routine financial actions
                            </h2>
                        </div>
                        <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                            Active
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div class="rounded-3xl border border-slate-200 bg-white/90 p-4 dark:border-white/10 dark:bg-zinc-950/60">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">Wallet operations</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-300">
                                Deposits, purchases, and service confirmations are placed in clearer blocks so users can review before committing.
                            </p>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-white/90 p-4 dark:border-white/10 dark:bg-zinc-950/60">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">Identity-aware services</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-300">
                                Funding accounts, KYC access, and sensitive features are positioned behind stricter profile and verification checks.
                            </p>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-white/90 p-4 dark:border-white/10 dark:bg-zinc-950/60">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">Serious support path</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-300">
                                When a transaction needs attention, the support flow stays visible instead of being buried behind unrelated interface noise.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="panel-card p-6 sm:p-8">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="section-kicker">Core services</p>
                <h2 class="mt-3 section-title">The main transaction paths people return to every day.</h2>
            </div>
            <p class="max-w-2xl section-copy">
                The product is focused on repeatable consumer payment actions, not decorative landing-page noise. Each area is designed to stay clear on smaller screens.
            </p>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($channels as $channel)
                <article class="stat-card" data-tone="{{ $channel['tone'] }}">
                    <div class="space-y-4">
                        <span class="stat-card-icon">
                            @switch($channel['icon'])
                                @case('wifi')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14 0"></path><path d="M1.5 8.5a16 16 0 0 1 21 0"></path><path d="M8.5 16.5a6 6 0 0 1 7 0"></path><circle cx="12" cy="20" r="1"></circle></svg>
                                    @break
                                @case('bolt')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h7l-1 8 10-12h-7z"></path></svg>
                                    @break
                                @case('wallet')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><path d="M16 12h4"></path></svg>
                                    @break
                                @default
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                            @endswitch
                        </span>
                        <div>
                            <p class="stat-label">{{ $channel['title'] }}</p>
                            <p class="mt-2 stat-meta">{{ $channel['copy'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1fr_1fr]">
        <div class="panel-card p-6 sm:p-8">
            <p class="section-kicker">Operating principles</p>
            <h2 class="mt-3 section-title">Modernized for clarity, not for noise.</h2>
            <div class="mt-6 space-y-4">
                @foreach ($operatingPillars as $pillar)
                    <article class="rounded-3xl border border-slate-200 bg-slate-50/85 p-5 dark:border-white/10 dark:bg-white/5">
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-white">{{ $pillar['title'] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-300">{{ $pillar['copy'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="panel-card p-6 sm:p-8">
            <p class="section-kicker">Workflow</p>
            <h2 class="mt-3 section-title">A short path from onboarding to repeated usage.</h2>
            <div class="mt-6 space-y-4">
                @foreach ($workflow as $item)
                    <article class="flex gap-4 rounded-3xl border border-slate-200 bg-slate-50/85 p-5 dark:border-white/10 dark:bg-white/5">
                        <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">
                            {{ $item['step'] }}
                        </span>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-950 dark:text-white">{{ $item['title'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-300">{{ $item['copy'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="panel-card p-6 sm:p-8">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="section-kicker">Connected rails</p>
                <h2 class="mt-3 section-title">Networks and infrastructure that support the day-to-day use case.</h2>
            </div>
            <p class="max-w-2xl section-copy">
                The service set is organized around the payment and telecom channels users expect to access frequently.
            </p>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-6">
            @foreach ($partnerLogos as $logo)
                <div class="flex min-h-[112px] items-center justify-center rounded-3xl border border-slate-200 bg-slate-50/85 p-5 dark:border-white/10 dark:bg-white/5">
                    <img src="{{ asset($logo['src']) }}" alt="{{ $logo['alt'] }}" class="h-9 w-full object-contain sm:h-10">
                </div>
            @endforeach
        </div>
    </section>

    <section class="panel-card overflow-hidden p-6 sm:p-8">
        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div class="space-y-4">
                <p class="section-kicker">Ready to start</p>
                <h2 class="section-title">Open the dashboard and work from a cleaner transaction surface.</h2>
                <p class="section-copy max-w-2xl">
                    Whether you are handling routine recharges, wallet activity, or a more security-sensitive service, the interface is designed to stay calmer and more readable than the older billing patterns people are used to.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ $primaryAction['route'] }}" class="inline-flex h-12 items-center rounded-full bg-slate-950 px-6 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                        {{ $primaryAction['label'] }}
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex h-12 items-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                        Speak with support
                    </a>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-slate-50/90 p-5 dark:border-white/10 dark:bg-white/5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-zinc-950/60">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">User area</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">Responsive</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-zinc-950/60">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Controls</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">Stronger</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-zinc-950/60">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Purchases</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">Faster</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-zinc-950/60">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Support</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">Visible</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>
@endsection
