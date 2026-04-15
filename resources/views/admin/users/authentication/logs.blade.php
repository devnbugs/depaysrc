@extends('admin.layouts.master')

@section('content')

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div>
            <div class="flex flex-wrap gap-2">
                <span class="section-kicker">Administration</span>
                <span class="section-kicker">Audit</span>
            </div>

            <div class="space-y-4">
                <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Authentication Verification Logs
                </h2>
                <p class="max-w-2xl section-copy">
                    Comprehensive audit trail of all authentication attempts across PIN, 2FA, and Passkey methods.
                </p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <section class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
        <form method="GET" action="{{ route('admin.auth.logs') }}" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Type Filter -->
                <div>
                    <label for="type" class="block text-sm font-semibold text-slate-700 dark:text-white">Method</label>
                    <select name="type" id="type" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-900 dark:border-zinc-600 dark:bg-slate-800 dark:text-white">
                        <option value="">All Methods</option>
                        <option value="pin" {{ request('type') === 'pin' ? 'selected' : '' }}>PIN</option>
                        <option value="two_factor" {{ request('type') === 'two_factor' ? 'selected' : '' }}>Two-Factor</option>
                        <option value="passkey" {{ request('type') === 'passkey' ? 'selected' : '' }}>Passkey</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700 dark:text-white">Status</label>
                    <select name="status" id="status" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-900 dark:border-zinc-600 dark:bg-slate-800 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <!-- Context Filter -->
                <div>
                    <label for="context" class="block text-sm font-semibold text-slate-700 dark:text-white">Context</label>
                    <select name="context" id="context" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-900 dark:border-zinc-600 dark:bg-slate-800 dark:text-white">
                        <option value="">All Contexts</option>
                        <option value="login" {{ request('context') === 'login' ? 'selected' : '' }}>Login</option>
                        <option value="payment" {{ request('context') === 'payment' ? 'selected' : '' }}>Payment</option>
                        <option value="withdrawal" {{ request('context') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                        <option value="transfer" {{ request('context') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="settings" {{ request('context') === 'settings' ? 'selected' : '' }}>Settings</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label for="date_range" class="block text-sm font-semibold text-slate-700 dark:text-white">Date Range</label>
                    <select name="date_range" id="date_range" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-900 dark:border-zinc-600 dark:bg-slate-800 dark:text-white">
                        <option value="7" {{ request('date_range') === '7' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30" {{ request('date_range') === '30' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="90" {{ request('date_range') === '90' ? 'selected' : '' }}>Last 90 Days</option>
                        <option value="all" {{ request('date_range') === 'all' ? 'selected' : '' }}>All Time</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 dark:hover:bg-blue-500">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
                    Filter
                </button>
                <a href="{{ route('admin.auth.logs') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-zinc-600 dark:text-zinc-300 dark:hover:bg-slate-800">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <!-- Logs Table -->
    <section class="rounded-2xl border border-slate-200 bg-white dark:border-white/10 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-white/10">
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">User</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Context</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="border-b border-slate-100 hover:bg-slate-50 dark:border-white/5 dark:hover:bg-white/5">
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('admin.users.auth', $log->user_id) }}" class="font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $log->user->name }}
                                </a>
                                <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $log->user->email }}</p>
                            </td>

                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                                    @switch($log->type)
                                        @case('pin')
                                            style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;"
                                            @break
                                        @case('two_factor')
                                            style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;"
                                            @break
                                        @case('passkey')
                                            style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;"
                                            @break
                                    @endswitch
                                >
                                    {{ ucfirst(str_replace('_', ' ', $log->type)) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm">
                                <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-white/10 dark:text-zinc-300">
                                    {{ ucfirst(str_replace('_', ' ', $log->context)) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm">
                                @if($log->status === 'verified')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"></path></svg>
                                        Verified
                                    </span>
                                @elseif($log->status === 'failed')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">
                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"></path></svg>
                                        Failed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-zinc-400">
                                {{ $log->created_at->format('M d, Y H:i') }}
                                <br>
                                <span class="text-xs">{{ $log->created_at->diffForHumans() }}</span>
                            </td>

                            <td class="px-6 py-4 text-sm">
                                <button onclick="showDetails({{ $log->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-zinc-400">
                                No verification logs found matching your filters
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="border-t border-slate-200 px-6 py-4 dark:border-white/10">
                {{ $logs->links() }}
            </div>
        @endif
    </section>

    <!-- Log Summary -->
    <section class="grid gap-6 sm:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Total Attempts</p>
            <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-white">{{ number_format($summary['total'] ?? 0) }}</p>
            <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">In selected period</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Successful</p>
            <p class="mt-3 text-3xl font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($summary['verified'] ?? 0) }}</p>
            <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">{{ $summary['total'] > 0 ? round(($summary['verified'] / $summary['total']) * 100, 1) : 0 }}% success rate</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Failed</p>
            <p class="mt-3 text-3xl font-semibold text-rose-600 dark:text-rose-400">{{ number_format($summary['failed'] ?? 0) }}</p>
            <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">{{ $summary['total'] > 0 ? round(($summary['failed'] / $summary['total']) * 100, 1) : 0 }}% failure rate</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Pending</p>
            <p class="mt-3 text-3xl font-semibold text-amber-600 dark:text-amber-400">{{ number_format($summary['pending'] ?? 0) }}</p>
            <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">Awaiting verification</p>
        </div>
    </section>
</section>

@endsection
