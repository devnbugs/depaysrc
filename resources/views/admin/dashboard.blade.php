@extends('admin.layouts.master')

@section('content')
@php
    $heroStats = [
        [
            'label' => 'Total users',
            'value' => number_format($widget['total_users']),
            'meta' => 'Registered accounts across the platform.',
            'tone' => 'sky',
            'icon' => 'users',
        ],
        [
            'label' => 'Verified users',
            'value' => number_format($widget['verified_users']),
            'meta' => 'Accounts that cleared verification.',
            'tone' => 'emerald',
            'icon' => 'verified',
        ],
        [
            'label' => 'Successful purchases',
            'value' => number_format($payment['total_purchase_success']),
            'meta' => 'Bill and recharge orders completed.',
            'tone' => 'violet',
            'icon' => 'cart',
        ],
        [
            'label' => 'Pending withdrawals',
            'value' => number_format($paymentWithdraw['total_withdraw_pending']),
            'meta' => 'Withdrawal requests still in queue.',
            'tone' => 'amber',
            'icon' => 'clock',
        ],
    ];

    $countStats = [
        [
            'label' => 'Email unverified',
            'value' => number_format($widget['email_unverified_users']),
            'meta' => 'Users yet to confirm email.',
            'tone' => 'rose',
        ],
        [
            'label' => 'SMS unverified',
            'value' => number_format($widget['sms_unverified_users']),
            'meta' => 'Users yet to confirm phone.',
            'tone' => 'slate',
        ],
        [
            'label' => 'Failed purchases',
            'value' => number_format($payment['total_purchase_failed']),
            'meta' => 'Orders that did not complete.',
            'tone' => 'rose',
        ],
        [
            'label' => 'User balances',
            'value' => $general->cur_sym . showAmount($payment['total_users_balance']),
            'meta' => 'Combined balance across verified users.',
            'tone' => 'emerald',
        ],
    ];

    $financialStats = [
        [
            'label' => 'Successful deposits',
            'value' => $general->cur_sym . showAmount($payment['total_deposit_amount']),
            'meta' => 'Total inbound deposit volume.',
            'tone' => 'sky',
        ],
        [
            'label' => 'Deposit charges',
            'value' => $general->cur_sym . showAmount($payment['total_deposit_charge']),
            'meta' => 'Profit retained from deposits.',
            'tone' => 'violet',
        ],
        [
            'label' => 'Total withdrawals',
            'value' => $general->cur_sym . showAmount($paymentWithdraw['total_withdraw_amount']),
            'meta' => 'Approved withdrawal volume.',
            'tone' => 'amber',
        ],
        [
            'label' => 'Withdrawal charges',
            'value' => $general->cur_sym . showAmount($paymentWithdraw['total_withdraw_charge']),
            'meta' => 'Charges collected from withdrawals.',
            'tone' => 'rose',
        ],
    ];

    $quickActions = [
        ['label' => 'Users', 'copy' => 'Review account records and activity.', 'route' => route('admin.users.all'), 'tone' => 'sky', 'icon' => 'users'],
        ['label' => 'Deposits', 'copy' => 'Audit successful and pending deposits.', 'route' => route('admin.deposit.list'), 'tone' => 'emerald', 'icon' => 'wallet'],
        ['label' => 'Withdrawals', 'copy' => 'Track approved and pending payouts.', 'route' => route('admin.withdraw.log'), 'tone' => 'amber', 'icon' => 'cash'],
        ['label' => 'Reports', 'copy' => 'Inspect transaction and sales trends.', 'route' => route('admin.report.transaction'), 'tone' => 'violet', 'icon' => 'chart'],
        ['label' => 'Settings', 'copy' => 'Adjust platform and payment rules.', 'route' => route('admin.setting.index'), 'tone' => 'slate', 'icon' => 'settings'],
        ['label' => 'Support', 'copy' => 'Handle user tickets and follow-ups.', 'route' => route('admin.users.open.ticket'), 'tone' => 'rose', 'icon' => 'chat'],
    ];

    $monthLabels = $months->values();
    $depositSeries = $months->map(fn ($month) => getAmount(@$depositsMonth->where('months', $month)->first()->depositAmount))->values();
    $withdrawSeries = $months->map(fn ($month) => getAmount(@$withdrawalMonth->where('months', $month)->first()->withdrawAmount))->values();
    $depositDayLabels = $deposits['per_day']->flatten();
    $depositDaySeries = $deposits['per_day_amount']->flatten();
    $withdrawDayLabels = $withdrawals['per_day']->flatten();
    $withdrawDaySeries = $withdrawals['per_day_amount']->flatten();
@endphp

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="space-y-6">
                <div class="flex flex-wrap gap-2">
                    <span class="section-kicker">Admin console</span>
                    <span class="section-kicker">Flux layout</span>
                </div>

                <div class="space-y-4">
                    <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                        Manage the platform from one calm command center.
                    </h2>
                    <p class="max-w-2xl section-copy">
                        Review users, deposits, withdrawals, gateway performance, and reports without the dashboard fighting your attention.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.users.all') }}" class="inline-flex h-11 items-center rounded-full bg-slate-950 px-5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                        Open users
                    </a>
                    <a href="{{ route('admin.deposit.list') }}" class="inline-flex h-11 items-center rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                        View deposits
                    </a>
                    <a href="{{ route('admin.report.transaction') }}" class="inline-flex h-11 items-center rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                        Open reports
                    </a>
                </div>

                <div class="action-grid">
                    @foreach ($quickActions as $action)
                        <a href="{{ $action['route'] }}" class="action-tile" data-tone="{{ $action['tone'] }}">
                            <span class="action-tile-icon">
                                @switch($action['icon'])
                                    @case('users')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        @break
                                    @case('wallet')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><path d="M16 12h4"></path></svg>
                                        @break
                                    @case('cash')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3 3 0 0 1 0 6H6"></path></svg>
                                        @break
                                    @case('chart')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19V5"></path><path d="M8 19v-8"></path><path d="M12 19v-5"></path><path d="M16 19v-11"></path><path d="M20 19V9"></path></svg>
                                        @break
                                    @case('settings')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 0 0 .33 1.86l.05.05a2 2 0 0 1-1.41 3.41l-.05-.05A1.7 1.7 0 0 0 17 20.6a1.7 1.7 0 0 0-1 .3 1.7 1.7 0 0 0-.7 1.18V22a2 2 0 0 1-4 0v-.05a1.7 1.7 0 0 0-.7-1.18 1.7 1.7 0 0 0-1-.3 1.7 1.7 0 0 0-1.35.58l-.05.05A2 2 0 0 1 4.2 17l.05-.05A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.3-1 1.7 1.7 0 0 0-1.18-.7H3a2 2 0 0 1 0-4h.12a1.7 1.7 0 0 0 1.18-.7 1.7 1.7 0 0 0 .3-1 1.7 1.7 0 0 0-.58-1.35l-.05-.05A2 2 0 0 1 6.4 4.6l.05.05A1.7 1.7 0 0 0 8 4.3a1.7 1.7 0 0 0 1-.3 1.7 1.7 0 0 0 .7-1.18V3a2 2 0 0 1 4 0v.12a1.7 1.7 0 0 0 .7 1.18 1.7 1.7 0 0 0 1 .3 1.7 1.7 0 0 0 1.35-.58l.05-.05A2 2 0 0 1 19.8 7l-.05.05A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 .3 1 1.7 1.7 0 0 0 1.18.7H21a2 2 0 0 1 0 4h-.12a1.7 1.7 0 0 0-1.18.7 1.7 1.7 0 0 0-.3 1z"></path></svg>
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
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($heroStats as $stat)
                    <article class="stat-card" data-tone="{{ $stat['tone'] }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-3">
                                <span class="stat-card-icon">
                                    @switch($stat['icon'])
                                        @case('users')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                            @break
                                        @case('verified')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 12 2 2 4-4"></path><circle cx="12" cy="12" r="9"></circle></svg>
                                            @break
                                        @case('cart')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.6L23 6H6"></path></svg>
                                            @break
                                        @case('clock')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 3"></path></svg>
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
        @foreach ($countStats as $stat)
            <article class="stat-card" data-tone="{{ $stat['tone'] }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-3">
                        <span class="stat-card-icon">
                            @switch($loop->index)
                                @case(0)
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                    @break
                                @case(1)
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18"></path><path d="M3 12h18"></path><path d="M3 17h18"></path></svg>
                                    @break
                                @case(2)
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V4"></path><path d="M5 13l7 7 7-7"></path></svg>
                                    @break
                                @default
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><path d="M16 12h4"></path></svg>
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

    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <section class="panel-card p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="section-kicker">Monthly trend</p>
                    <h3 class="mt-3 section-title">Deposits versus withdrawals.</h3>
                </div>
            </div>
            <div id="apex-bar-chart" class="mt-6 min-h-[22rem]"></div>
        </section>

        <section class="panel-card p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="section-kicker">Financial summary</p>
                    <h3 class="mt-3 section-title">Core money movement.</h3>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                @foreach ($financialStats as $stat)
                    <article class="stat-card" data-tone="{{ $stat['tone'] }}">
                        <div class="space-y-3">
                            <span class="stat-card-icon">
                                @switch($loop->index)
                                    @case(0)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v16"></path><path d="M5 11l7-7 7 7"></path></svg>
                                        @break
                                    @case(1)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v16"></path><path d="M5 11l7-7 7 7"></path></svg>
                                        @break
                                    @case(2)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V4"></path><path d="M5 13l7 7 7-7"></path></svg>
                                        @break
                                    @default
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V4"></path><path d="M5 13l7 7 7-7"></path></svg>
                                @endswitch
                            </span>
                            <div>
                                <p class="stat-label">{{ $stat['label'] }}</p>
                                <p class="mt-2 stat-value text-xl">{{ $stat['value'] }}</p>
                                <p class="mt-2 stat-meta">{{ $stat['meta'] }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="panel-card p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="section-kicker">Deposit history</p>
                    <h3 class="mt-3 section-title">Last 30 days deposit flow.</h3>
                </div>
            </div>
            <div id="deposit-line" class="mt-6 min-h-[20rem]"></div>
        </section>

        <section class="panel-card p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="section-kicker">Withdrawal history</p>
                    <h3 class="mt-3 section-title">Last 30 days withdrawal flow.</h3>
                </div>
            </div>
            <div id="withdraw-line" class="mt-6 min-h-[20rem]"></div>
        </section>
    </div>
</section>
@endsection

@push('script')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const currency = @json($general->cur_sym);

            new ApexCharts(document.querySelector('#apex-bar-chart'), {
                series: [
                    { name: 'Deposit', data: @json($depositSeries) },
                    { name: 'Withdraw', data: @json($withdrawSeries) },
                ],
                chart: { type: 'bar', height: 360, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                plotOptions: { bar: { horizontal: false, columnWidth: '52%', borderRadius: 10 } },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: { categories: @json($monthLabels), labels: { style: { colors: '#64748b' } } },
                yaxis: { labels: { style: { colors: '#64748b' }, formatter: (val) => currency + val.toFixed(0) } },
                grid: { borderColor: 'rgba(148, 163, 184, 0.18)' },
                fill: { opacity: 1 },
                colors: ['#0ea5e9', '#10b981'],
                tooltip: { y: { formatter: (val) => currency + val.toFixed(2) } },
            }).render();

            new ApexCharts(document.querySelector('#deposit-line'), {
                chart: { type: 'area', height: 320, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Deposit', data: @json($depositDaySeries) }],
                xaxis: { categories: @json($depositDayLabels), labels: { style: { colors: '#64748b' } } },
                colors: ['#0ea5e9'],
                stroke: { curve: 'smooth', width: 3 },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] },
                },
                grid: { borderColor: 'rgba(148, 163, 184, 0.18)' },
                dataLabels: { enabled: false },
                tooltip: { y: { formatter: (val) => currency + val.toFixed(2) } },
            }).render();

            new ApexCharts(document.querySelector('#withdraw-line'), {
                chart: { type: 'area', height: 320, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                series: [{ name: 'Withdraw', data: @json($withdrawDaySeries) }],
                xaxis: { categories: @json($withdrawDayLabels), labels: { style: { colors: '#64748b' } } },
                colors: ['#f97316'],
                stroke: { curve: 'smooth', width: 3 },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] },
                },
                grid: { borderColor: 'rgba(148, 163, 184, 0.18)' },
                dataLabels: { enabled: false },
                tooltip: { y: { formatter: (val) => currency + val.toFixed(2) } },
            }).render();
        });
    </script>
@endpush
