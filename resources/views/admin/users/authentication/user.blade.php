@extends('admin.layouts.master')

@section('content')

<section class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.all') }}" class="text-slate-500 hover:text-slate-700 dark:text-zinc-400 dark:hover:text-zinc-300">
                    <span class="section-kicker">Users</span>
                </a>
                <span class="section-kicker">Authentication</span>
            </div>
            <h2 class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">
                {{ $user->name }}
            </h2>
            <p class="mt-1 text-sm text-slate-600 dark:text-zinc-400">{{ $user->email }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-zinc-400">User ID</p>
            <p class="text-2xl font-mono font-semibold text-slate-700 dark:text-zinc-300">#{{ $user->id }}</p>
        </div>
    </div>

    <!-- Authentication Methods Row -->
    <section class="grid gap-6 sm:grid-cols-3">
        <!-- PIN Status -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">PIN Authentication</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">
                        @if($user->isPinEnabled())
                            <span class="text-emerald-600 dark:text-emerald-400">Active</span>
                        @elseif($user->pin_enabled === false)
                            <span class="text-amber-600 dark:text-amber-400">Disabled</span>
                        @else
                            <span class="text-slate-600 dark:text-zinc-400">Not Set</span>
                        @endif
                    </p>
                    @if($user->pin_locked_until && $user->pin_locked_until > now())
                        <p class="mt-2 text-xs text-rose-600 dark:text-rose-400">🔒 Locked until {{ $user->pin_locked_until->format('M d, H:i') }}</p>
                    @endif
                    @if($user->pin_enabled_at)
                        <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">Set {{ $user->pin_enabled_at->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
            @if($user->isPinEnabled() || $user->pin_locked_until)
                <div class="mt-4 flex gap-2">
                    @if($user->pin_locked_until && $user->pin_locked_until > now())
                        <form action="{{ route('admin.users.pin.unlock', $user->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full text-xs font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                Unlock
                            </button>
                        </form>
                    @endif
                    @if($user->isPinEnabled())
                        <form action="{{ route('admin.users.pin.reset', $user->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" onclick="return confirm('Reset PIN for this user?')" class="w-full text-xs font-semibold text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300">
                                Reset
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <!-- 2FA Status -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Two-Factor Auth</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">
                        @if($user->isTwoFactorEnabled())
                            <span class="text-blue-600 dark:text-blue-400">Active</span>
                        @else
                            <span class="text-slate-600 dark:text-zinc-400">Disabled</span>
                        @endif
                    </p>
                    @if($user->isTwoFactorEnabled())
                        <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">Google Authenticator</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">Enabled {{ $user->two_factor_enabled_at->diffForHumans() }}</p>
                    @else
                        <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">No 2FA configured</p>
                    @endif
                </div>
            </div>
            @if($user->isTwoFactorEnabled())
                <form action="{{ route('admin.users.2fa.disable', $user->id) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" onclick="return confirm('Disable 2FA for this user?')" class="w-full text-xs font-semibold text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300">
                        Disable 2FA
                    </button>
                </form>
            @endif
        </div>

        <!-- Passkey Status -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Passkeys</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">
                        {{ $user->passkeys_count ?? 0 }}
                    </p>
                    @if(($user->passkeys_count ?? 0) > 0)
                        <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">
                            {{ $user->passkeys_count === 1 ? 'credential' : 'credentials' }} registered
                        </p>
                    @else
                        <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">No passkeys set up</p>
                    @endif
                </div>
            </div>
            @if(($user->passkeys_count ?? 0) > 0)
                <form action="{{ route('admin.users.passkeys.disable', $user->id) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" onclick="return confirm('Remove all passkeys for this user?')" class="w-full text-xs font-semibold text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300">
                        Remove All
                    </button>
                </form>
            @endif
        </div>
    </section>

    <!-- Authentication Verification History -->
    <section class="rounded-2xl border border-slate-200 bg-white dark:border-white/10 dark:bg-slate-900">
        <div class="border-b border-slate-200 p-6 dark:border-white/10">
            <p class="section-kicker">Activity</p>
            <h3 class="mt-3 section-title">Recent Verification Attempts</h3>
        </div>

        <div class="divide-y divide-slate-200 dark:divide-white/10">
            @forelse($verifications as $verification)
                <div class="flex items-start justify-between px-6 py-4 hover:bg-slate-50 dark:hover:bg-white/5">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            @switch($verification->type)
                                @case('pin')
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    </span>
                                    @break
                                @case('two_factor')
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                    </span>
                                    @break
                                @case('passkey')
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                    </span>
                                    @break
                            @endswitch

                            <div>
                                <p class="font-semibold text-slate-700 dark:text-zinc-300">
                                    {{ ucfirst($verification->type) }} - {{ ucfirst(str_replace('_', ' ', $verification->context)) }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">
                                    {{ $verification->created_at->format('M d, Y H:i:s') }} ({{ $verification->created_at->diffForHumans() }})
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="ml-4 flex items-center gap-2">
                        @if($verification->status === 'verified')
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"></path></svg>
                                Verified
                            </span>
                        @elseif($verification->status === 'failed')
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
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center">
                    <p class="text-slate-500 dark:text-zinc-400">No verification attempts on record</p>
                </div>
            @endforelse
        </div>

        @if($verifications->hasPages())
            <div class="border-t border-slate-200 px-6 py-4 dark:border-white/10">
                {{ $verifications->links() }}
            </div>
        @endif
    </section>
</section>

@endsection
