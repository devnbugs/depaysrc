@extends($activeTemplate.'layouts.dashboard')

@section('content')
@php
    $displayName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? '')) ?: ($user->username ?? 'there');
    $heroStats = [
        [
            'label' => 'Wallet balance',
            'value' => $general->cur_sym . showAmount($user->balance),
            'meta' => 'Available for purchases, deposits, and transfers.',
            'tone' => 'sky',
            'icon' => 'wallet',
        ],
        [
            'label' => 'Total deposits',
            'value' => $general->cur_sym . showAmount($totalDeposit),
            'meta' => 'Money that has come into the wallet over time.',
            'tone' => 'emerald',
            'icon' => 'deposit',
        ],
        [
            'label' => 'Total withdrawals',
            'value' => $general->cur_sym . showAmount($totalWithdraw),
            'meta' => 'Funds moved out of the wallet.',
            'tone' => 'amber',
            'icon' => 'withdraw',
        ],
        [
            'label' => 'Security status',
            'value' => ($user->pin !== null && (int) $user->pin_state === 1) ? 'PIN on' : 'PIN needed',
            'meta' => ($user->pin !== null && (int) $user->pin_state === 1) ? 'Your transactions are protected.' : 'Set or activate your PIN for safer purchases.',
            'tone' => 'slate',
            'icon' => 'shield',
        ],
    ];

    $accountStats = [
        [
            'label' => 'Pending deposits',
            'value' => $general->cur_sym . showAmount($PDeposit),
            'meta' => 'Awaiting approval or confirmation.',
            'tone' => 'violet',
        ],
        [
            'label' => 'Pending withdrawals',
            'value' => $general->cur_sym . showAmount($PWithdraw),
            'meta' => 'Waiting on processing.',
            'tone' => 'rose',
        ],
        [
            'label' => 'Savings this year',
            'value' => $general->cur_sym . showAmount($saved),
            'meta' => 'Your accumulated savings balance.',
            'tone' => 'emerald',
        ],
        [
            'label' => 'Loan balance',
            'value' => $general->cur_sym . showAmount($bal),
            'meta' => 'Current outstanding loan amount.',
            'tone' => 'amber',
        ],
    ];

    $quickActions = [
        ['label' => 'Deposit funds', 'copy' => 'Top up your wallet instantly.', 'route' => route('user.deposit'), 'tone' => 'sky', 'icon' => 'wallet'],
        ['label' => 'Cards', 'copy' => 'Create or manage your virtual cards.', 'route' => route('user.vcard'), 'tone' => 'sky', 'icon' => 'card'],
        ['label' => 'Transfer', 'copy' => 'Send money to any bank account.', 'route' => route('user.othertransfer'), 'tone' => 'slate', 'icon' => 'send'],
        ['label' => 'Buy data', 'copy' => 'Choose a bundle and go online.', 'route' => route('user.internet'), 'tone' => 'emerald', 'icon' => 'wifi'],
        ['label' => 'Buy airtime', 'copy' => 'Recharge any network in seconds.', 'route' => route('user.airtime'), 'tone' => 'violet', 'icon' => 'phone'],
        ['label' => 'Cable TV', 'copy' => 'Renew your TV subscription fast.', 'route' => route('user.cabletv'), 'tone' => 'amber', 'icon' => 'tv'],
        ['label' => 'Utilities', 'copy' => 'Pay electricity and related bills.', 'route' => route('user.utility'), 'tone' => 'rose', 'icon' => 'bolt'],
        ['label' => 'KYC Services', 'copy' => 'Upgrade with Paystack and unlock verification tools.', 'route' => route('user.kyc.services'), 'tone' => 'sky', 'icon' => 'shield'],
        ['label' => 'Support', 'copy' => 'Ask us to review any payment issue.', 'route' => route('user.support'), 'tone' => 'slate', 'icon' => 'chat'],
    ];

    $purchaseTitles = [
        1 => 'Airtime Purchase',
        2 => 'Data Purchase',
        3 => 'Cable TV',
        4 => 'Electricity Bill',
        5 => 'WAEC Reg.',
        6 => 'WAEC Result',
    ];

    $accounts = [];

    if (!empty($user->bN1) && !empty($user->aNo1) && !empty($user->aN1)) {
        $accounts[] = [
            'bank' => $user->bN1,
            'number' => $user->aNo1,
            'name' => trim(str_replace('HPDATASERVICE', '', $user->aN1), '/'),
        ];
    }

    if (!empty($user->bN2) && !empty($user->aNo2) && !empty($user->aN2)) {
        $accounts[] = [
            'bank' => $user->bN2,
            'number' => $user->aNo2,
            'name' => trim(str_replace('HPDATASERVICE', '', $user->aN2), '/'),
        ];
    }

    if (!empty($user->bN3) && !empty($user->aNo3) && !empty($user->aN3)) {
        $accounts[] = [
            'bank' => $user->bN3,
            'number' => $user->aNo3,
            'name' => trim(str_replace('HPDATASERVICE', '', $user->aN3), '/'),
        ];
    }
@endphp

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            <div class="space-y-6">
                <div class="flex flex-wrap gap-2">
                    <span class="section-kicker">User dashboard</span>
                    <span class="section-kicker">Flux layout</span>
                </div>

                <div class="space-y-4">
                    <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                        Welcome back, {{ $displayName }}.
                    </h2>
                    <p class="max-w-2xl section-copy">
                        Manage deposits, airtime, data, utility payments, and account activity from one calm, responsive workspace.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('user.deposit') }}" class="inline-flex h-11 items-center rounded-full bg-slate-950 px-5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                        Fund wallet
                    </a>
                    <a href="{{ route('user.internet') }}" class="inline-flex h-11 items-center rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                        Buy data
                    </a>
                    <a href="{{ route('user.profile.setting') }}" class="inline-flex h-11 items-center rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                        Edit profile
                    </a>
                    <a href="{{ route('user.kyc.services') }}" class="inline-flex h-11 items-center rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                        KYC services
                    </a>
                </div>

                @if ($broadcast && !empty($broadcast->message))
                    <div class="rounded-3xl border border-sky-200 bg-sky-50/90 p-4 text-sky-950 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-50">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-700 dark:text-sky-300">Announcement</p>
                        <p class="mt-2 text-sm leading-6">{{ $broadcast->message }}</p>
                    </div>
                @endif

                @if ($user->pin === null || (int) $user->pin_state === 0)
                    <div class="rounded-3xl border border-amber-200 bg-amber-50/90 p-4 text-amber-950 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-50">
                        <p class="font-semibold">Security check</p>
                        <p class="mt-2 text-sm leading-6">Your transaction PIN is not fully active yet. Set or enable it before making purchases.</p>
                        <a href="{{ route('user.user.pin.index') }}" class="mt-3 inline-flex h-10 items-center rounded-full bg-amber-500 px-4 text-sm font-semibold text-white transition hover:bg-amber-600">
                            Open PIN settings
                        </a>
                    </div>
                @endif
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($heroStats as $stat)
                    <article class="stat-card" data-tone="{{ $stat['tone'] }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-3">
                                <span class="stat-card-icon">
                                    @switch($stat['icon'])
                                        @case('wallet')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><path d="M16 12h4"></path></svg>
                                            @break
                                        @case('deposit')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v16"></path><path d="M5 11l7-7 7 7"></path></svg>
                                            @break
                                        @case('withdraw')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V4"></path><path d="M5 13l7 7 7-7"></path></svg>
                                            @break
                                        @case('shield')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                            @break
                                    @endswitch
                                </span>
                                <div>
                                    <p class="stat-label">{{ $stat['label'] }}</p>
                                    <p class="mt-2 stat-value">{{ $stat['value'] }}</p>
                                    <p class="mt-2 stat-meta">{{ $stat['meta'] }}</p>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($accountStats as $stat)
            <article class="stat-card" data-tone="{{ $stat['tone'] }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-3">
                        <span class="stat-card-icon">
                            @switch($loop->index)
                                @case(0)
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v16"></path><path d="M5 11l7-7 7 7"></path></svg>
                                    @break
                                @case(1)
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V4"></path><path d="M5 13l7 7 7-7"></path></svg>
                                    @break
                                @case(2)
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20V10"></path><path d="M10 20V4"></path><path d="M16 20v-8"></path><path d="M22 20V6"></path></svg>
                                    @break
                                @default
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3 3 0 0 1 0 6H6"></path></svg>
                            @endswitch
                        </span>
                        <div>
                            <p class="stat-label">{{ $stat['label'] }}</p>
                            <p class="mt-2 stat-value text-xl">{{ $stat['value'] }}</p>
                            <p class="mt-2 stat-meta">{{ $stat['meta'] }}</p>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <section class="panel-card p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-kicker">Quick actions</p>
                <h3 class="mt-3 section-title">Everything you use most is one tap away.</h3>
            </div>
            <p class="max-w-xl section-copy">No extra scrolling, no giant buttons, and no cramped layout on smaller screens.</p>
        </div>

        <div class="mt-6 action-grid">
            @foreach ($quickActions as $action)
                <a href="{{ $action['route'] }}" class="action-tile" data-tone="{{ $action['tone'] }}">
                    <span class="action-tile-icon">
                        @switch($action['icon'])
                            @case('wallet')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><path d="M16 12h4"></path></svg>
                                @break
                            @case('card')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><path d="M2 10h20"></path></svg>
                                @break
                            @case('send')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2 11 13"></path><path d="M22 2 15 22l-4-9-9-4 20-7z"></path></svg>
                                @break
                            @case('wifi')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14 0"></path><path d="M1.5 8.5a16 16 0 0 1 21 0"></path><path d="M8.5 16.5a6 6 0 0 1 7 0"></path><circle cx="12" cy="20" r="1"></circle></svg>
                                @break
                            @case('phone')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.4 19.4 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.07.6.21 1.19.41 1.76a2 2 0 0 1-.45 2.11L8 9.6a16 16 0 0 0 6.4 6.4l2-2a2 2 0 0 1 2.11-.45c.57.2 1.16.34 1.76.41A2 2 0 0 1 22 16.92z"></path></svg>
                                @break
                            @case('tv')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="12" rx="2"></rect><path d="M8 21h8"></path><path d="M12 3l-4 4h8z"></path></svg>
                                @break
                            @case('bolt')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h7l-1 8 10-12h-7z"></path></svg>
                                @break
                            @case('shield')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                @break
                            @default
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path></svg>
                        @endswitch
                    </span>
                    <div class="space-y-1">
                        <p class="action-tile-title">{{ $action['label'] }}</p>
                        <p class="action-tile-copy">{{ $action['copy'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[1fr_1.1fr]">
        <section class="panel-card p-6">
            <div class="dashboard-section-header">
                <div>
                    <p class="section-kicker">Funding accounts</p>
                    <h3 class="mt-3 section-title">Copy your receiving accounts quickly.</h3>
                </div>
                <p class="section-copy text-sm">Tap copy to share the account number.</p>
            </div>

            <div class="mt-6 grid gap-4">
                @forelse ($accounts as $account)
                    <article class="dashboard-stack-card dashboard-account-card">
                        <div class="dashboard-record">
                            <div class="space-y-2">
                                <p class="text-xs uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ $account['bank'] }}</p>
                                <p class="dashboard-account-number">{{ $account['number'] }}</p>
                                <p class="text-sm text-slate-600 dark:text-zinc-300">{{ $account['name'] }}</p>
                            </div>
                            <button type="button" class="copy-chip shrink-0" data-copy-value="{{ $account['number'] }}">
                                Copy
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-200 p-6 text-sm text-slate-500 dark:border-white/10 dark:text-zinc-400">
                        No receiving accounts are attached to this profile yet.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="panel-card p-6">
            <div class="dashboard-section-header">
                <div>
                    <p class="section-kicker">Recent purchases</p>
                    <h3 class="mt-3 section-title">Latest successful and pending orders.</h3>
                </div>
                <a href="{{ route('user.deposit.history') }}" class="dashboard-inline-action">Deposit history</a>
            </div>

            <div class="mt-6 space-y-3">
                @forelse ($bills as $bill)
                    <article class="dashboard-stack-card">
                        <div class="dashboard-record">
                            <div class="dashboard-record-main">
                                <span class="inline-flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 dark:bg-white/5 dark:text-white">
                                    @switch($bill->type)
                                        @case(1)
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94m-1 7.98v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                            @break
                                        @case(2)
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
                                            @break
                                        @case(3)
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
                                            @break
                                        @case(4)
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                            @break
                                        @default
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                    @endswitch
                                </span>
                                <div class="dashboard-record-copy">
                                    <a href="{{ route('user.beta.receipt', ['billId' => $bill->id]) }}" class="block text-base font-semibold text-slate-950 transition hover:text-sky-700 dark:text-white dark:hover:text-sky-300">
                                        {{ $purchaseTitles[$bill->type] ?? 'Purchase' }}
                                    </a>
                                    <p class="dashboard-record-meta">
                                        {{ $bill->phone }} · {{ strtoupper($bill->network) }}
                                        @if (!empty($bill->plan))
                                            · {{ strtoupper($bill->plan) }}
                                        @endif
                                    </p>
                                    <p class="mt-1 text-xs uppercase tracking-[0.18em] text-slate-400 dark:text-zinc-500">Ref {{ $bill->trx }}</p>
                                </div>
                            </div>

                            <div class="dashboard-record-side">
                                <p class="text-base font-semibold text-slate-950 dark:text-white">{{ $general->cur_sym }}{{ showAmount($bill->amount) }}</p>
                                @if($bill->status == 0)
                                    <span class="mt-2 inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700 ring-1 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-500/20">Pending</span>
                                @elseif($bill->status == 1)
                                    <span class="mt-2 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-200 dark:ring-emerald-500/20">Completed</span>
                                @else
                                    <span class="mt-2 inline-flex rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-rose-700 ring-1 ring-rose-200 dark:bg-rose-500/10 dark:text-rose-200 dark:ring-rose-500/20">Declined</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-200 p-6 text-sm text-slate-500 dark:border-white/10 dark:text-zinc-400">
                        You do not have any recent purchases yet.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <section class="panel-card p-6">
            <div class="dashboard-section-header">
                <div>
                    <p class="section-kicker">Latest transactions</p>
                    <h3 class="mt-3 section-title">Recent wallet movement.</h3>
                </div>
            </div>

            <div class="mt-6 divide-y divide-slate-200/80 dark:divide-white/10">
                @forelse ($latestTrx as $trx)
                    <div class="dashboard-activity-row py-4 first:pt-0 last:pb-0">
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-950 dark:text-white">{{ $trx->details }}</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ showDateTime($trx->created_at) }}</p>
                        </div>
                        <div class="dashboard-activity-meta">
                            <p class="{{ $trx->trx_type === '-' ? 'text-rose-600' : 'text-emerald-600' }} text-sm font-semibold">
                                {{ $trx->trx_type }}{{ showAmount($trx->amount) }}
                            </p>
                            <p class="mt-1 text-xs uppercase tracking-[0.18em] text-slate-400 dark:text-zinc-500">
                                {{ $general->cur_sym }}{{ showAmount($trx->post_balance) }} {{ __($general->cur_text) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-200 p-6 text-sm text-slate-500 dark:border-white/10 dark:text-zinc-400">
                        {{ $emptyMessage }}
                    </div>
                @endforelse
            </div>
        </section>

        <section class="panel-card p-6">
            <div class="dashboard-section-header">
                <div>
                    <p class="section-kicker">Login activity</p>
                    <h3 class="mt-3 section-title">Recent sign-ins and security context.</h3>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                @forelse ($logins->take(4) as $login)
                    <article class="dashboard-stack-card">
                        <div class="dashboard-activity-row">
                            <div>
                                <p class="font-semibold text-slate-950 dark:text-white">{{ $login->browser ?? 'Browser' }}</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $login->os ?? 'Unknown OS' }} · {{ $login->country ?? 'Unknown country' }}</p>
                            </div>
                            <p class="dashboard-activity-meta text-xs uppercase tracking-[0.18em] text-slate-400 dark:text-zinc-500">{{ showDateTime($login->created_at) }}</p>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-200 p-6 text-sm text-slate-500 dark:border-white/10 dark:text-zinc-400">
                        No login records are available right now.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</section>
@endsection
