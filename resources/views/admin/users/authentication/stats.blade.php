@extends('admin.layouts.master')

@section('content')

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div>
            <div class="flex flex-wrap gap-2">
                <span class="section-kicker">Administration</span>
                <span class="section-kicker">Security</span>
            </div>

            <div class="space-y-4">
                <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Authentication Statistics
                </h2>
                <p class="max-w-2xl section-copy">
                    Monitor user security adoption including PIN, Two-Factor Authentication, and Passkey enrollment across your platform.
                </p>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Total Users -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Total Users</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-white">{{ number_format($stats['total_users']) }}</p>
                    <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">Active accounts on platform</p>
                </div>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-zinc-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
            </div>
        </div>

        <!-- PIN Enabled -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">PIN Enabled</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-white">
                        {{ number_format($stats['pin_enabled']) }}
                        <span class="text-lg text-emerald-600 dark:text-emerald-400">({{ $stats['pin_percentage'] }}%)</span>
                    </p>
                    <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">Users with active PIN</p>
                </div>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                </div>
            </div>
        </div>

        <!-- 2FA Enabled -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">2FA Enabled</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-white">
                        {{ number_format($stats['2fa_enabled']) }}
                        <span class="text-lg text-blue-600 dark:text-blue-400">({{ $stats['2fa_percentage'] }}%)</span>
                    </p>
                    <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">Users with 2FA active</p>
                </div>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Passkey Enabled -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Passkey Enabled</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-white">
                        {{ number_format($stats['passkey_enabled']) }}
                        <span class="text-lg text-purple-600 dark:text-purple-400">({{ $stats['passkey_percentage'] }}%)</span>
                    </p>
                    <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">Users with passkeys</p>
                </div>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
            </div>
        </div>

        <!-- PIN Disabled -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">PIN Disabled</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-white">{{ number_format($stats['pin_disabled']) }}</p>
                    <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">Users without PIN active</p>
                </div>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                </div>
            </div>
        </div>

        <!-- No PIN Set -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">PIN Not Set</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-white">{{ number_format($stats['pin_not_set']) }}</p>
                    <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">Users without PIN configured</p>
                </div>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Verification Attempts -->
    <section class="panel-card p-6">
        <div class="mb-6">
            <p class="section-kicker">Recent Activity</p>
            <h3 class="mt-3 section-title">Verification Attempts (Last 7 Days)</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-white/10">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Authentication Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Total Attempts</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Successful</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Failed</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Success Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($verifications as $v)
                        <tr class="border-b border-slate-100 hover:bg-slate-50 dark:border-white/5 dark:hover:bg-white/5">
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-zinc-300">
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                                    @switch($v->type)
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
                                    {{ ucfirst($v->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700 dark:text-zinc-300">{{ number_format($v->count) }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($v->successful) }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-rose-600 dark:text-rose-400">{{ $v->count - $v->successful }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-zinc-300">
                                    {{ $v->count > 0 ? round(($v->successful / $v->count) * 100, 1) : 0 }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center text-sm text-slate-500 dark:text-zinc-400">
                                No verification attempts in the last 7 days
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="grid gap-6 sm:grid-cols-2">
        <a href="{{ route('admin.auth.logs') }}" class="rounded-2xl border border-slate-200 bg-white p-6 transition hover:border-sky-300 hover:bg-sky-50 dark:border-white/10 dark:bg-slate-900 dark:hover:border-sky-500/30 dark:hover:bg-blue-900/10">
            <div class="flex items-start gap-4">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"></path><polyline points="16 5 16 1 8 1 8 5"></polyline><line x1="4" y1="13" x2="20" y2="13"></line></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-700 dark:text-zinc-300">View All Logs</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-400">Review detailed authentication verification history</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.users.all') }}" class="rounded-2xl border border-slate-200 bg-white p-6 transition hover:border-sky-300 hover:bg-sky-50 dark:border-white/10 dark:bg-slate-900 dark:hover:border-sky-500/30 dark:hover:bg-blue-900/10">
            <div class="flex items-start gap-4">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-white/10 dark:text-zinc-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-700 dark:text-zinc-300">Manage Users</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-400">View and manage user authentication settings</p>
                </div>
            </div>
        </a>
    </section>
</section>

@endsection
