@extends($activeTemplate.'layouts.dashboard')

@section('content')

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div>
            <div class="flex flex-wrap gap-2">
                <span class="section-kicker">Security</span>
                <span class="section-kicker">PIN Change</span>
            </div>

            <div class="space-y-4">
                <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Change your PIN
                </h2>
                <p class="max-w-2xl section-copy">
                    Update your 4-digit PIN to a new one. You'll need to verify your current PIN first.
                </p>
            </div>
        </div>
    </div>

    <section class="panel-card p-6 max-w-2xl">
        <form action="{{ route('pin.change') }}" method="post" class="space-y-6">
            @csrf

            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-6 dark:border-amber-500/30 dark:bg-amber-900/10">
                <h4 class="font-semibold text-amber-950 dark:text-amber-50 mb-2">Verify Current PIN</h4>
                <p class="text-sm text-amber-900 dark:text-amber-200">
                    For security, you'll need to enter your current PIN before setting a new one.
                </p>
            </div>

            <!-- Current PIN -->
            <div class="space-y-2">
                <label for="old_pin" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                    Current PIN <span class="text-rose-600">*</span>
                </label>
                <input 
                    type="password"
                    name="old_pin"
                    id="old_pin"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500 @error('old_pin') border-rose-500 dark:border-rose-500 @enderror"
                    placeholder="Enter current PIN"
                    maxlength="4"
                    inputmode="numeric"
                    pattern="[0-9]{4}"
                    required
                />
                @error('old_pin')
                    <p class="text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <hr class="border-slate-200 dark:border-white/10">

            <div>
                <h4 class="font-semibold text-slate-700 dark:text-zinc-300 mb-4">Set New PIN</h4>
                
                <!-- New PIN -->
                <div class="space-y-2 mb-4">
                    <label for="pin" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                        New PIN <span class="text-rose-600">*</span>
                    </label>
                    <input 
                        type="password"
                        name="pin"
                        id="pin"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500 @error('pin') border-rose-500 dark:border-rose-500 @enderror"
                        placeholder="Enter new 4-digit PIN"
                        maxlength="4"
                        inputmode="numeric"
                        pattern="[0-9]{4}"
                        required
                    />
                    @error('pin')
                        <p class="text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Verify PIN -->
                <div class="space-y-2">
                    <label for="verify_pin" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                        Confirm New PIN <span class="text-rose-600">*</span>
                    </label>
                    <input 
                        type="password"
                        name="verify_pin"
                        id="verify_pin"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500 @error('verify_pin') border-rose-500 dark:border-rose-500 @enderror"
                        placeholder="Re-enter new PIN"
                        maxlength="4"
                        inputmode="numeric"
                        pattern="[0-9]{4}"
                        required
                    />
                    @error('verify_pin')
                        <p class="text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="app-action-row border-t border-slate-200 pt-6 dark:border-white/10">
                <button type="submit" class="app-submit-button inline-flex h-11 rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                    Update PIN
                </button>
                <a href="{{ route('user.user.pin.pin') }}" class="inline-flex h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                    Cancel
                </a>
            </div>
        </form>
    </section>

    <!-- Lock Warning -->
    @if($user->isPinLocked())
        <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-6 dark:border-rose-500/30 dark:bg-rose-900/10">
            <div class="flex items-start gap-4">
                <svg class="h-6 w-6 text-rose-600 dark:text-rose-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div>
                    <h4 class="font-semibold text-rose-950 dark:text-rose-50">PIN Temporarily Locked</h4>
                    <p class="mt-1 text-sm text-rose-900 dark:text-rose-200">
                        Too many failed PIN attempts. Please try again in {{ $user->pin_locked_until->diffInMinutes(now()) }} minutes.
                    </p>
                </div>
            </div>
        </div>
    @endif
</section>

@endsection
