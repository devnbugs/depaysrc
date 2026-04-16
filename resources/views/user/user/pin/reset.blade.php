@extends($activeTemplate.'layouts.dashboard')

@section('content')

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div>
            <div class="flex flex-wrap gap-2">
                <span class="section-kicker">Security</span>
                <span class="section-kicker">PIN Reset</span>
            </div>

            <div class="space-y-4">
                <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Reset your PIN
                </h2>
                <p class="max-w-2xl section-copy">
                    If you've forgotten your PIN or haven't set one yet, you can reset it here using your account password for verification.
                </p>
            </div>
        </div>
    </div>

    <section class="panel-card p-6 max-w-2xl">
        <form action="{{ route('pin.reset') }}" method="post" class="space-y-6">
            @csrf

            <div class="rounded-2xl border border-blue-200 bg-blue-50/50 p-6 dark:border-blue-500/30 dark:bg-blue-900/10">
                <h4 class="font-semibold text-blue-950 dark:text-blue-50 mb-2">Password Verification Required</h4>
                <p class="text-sm text-blue-900 dark:text-blue-200">
                    For security purposes, resetting your PIN requires verification of your account password.
                </p>
            </div>

            <!-- Password Verification -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                    Account Password <span class="text-rose-600">*</span>
                </label>
                <input 
                    type="password"
                    name="password"
                    id="password"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500 @error('password') border-rose-500 dark:border-rose-500 @enderror"
                    placeholder="Enter your account password"
                    required
                />
                @error('password')
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

            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-6 dark:border-amber-500/30 dark:bg-amber-900/10">
                <h4 class="font-semibold text-amber-950 dark:text-amber-50 mb-3">PIN Requirements</h4>
                <ul class="space-y-1 text-sm text-amber-900 dark:text-amber-200">
                    <li>• Must be exactly 4 digits</li>
                    <li>• Cannot be all same digits (1111, 2222, etc.)</li>
                    <li>• Keep it confidential</li>
                </ul>
            </div>

            <div class="app-action-row border-t border-slate-200 pt-6 dark:border-white/10">
                <button type="submit" class="app-submit-button inline-flex h-11 rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                    Reset PIN
                </button>
                <a href="{{ route('user.user.pin.pin') }}" class="inline-flex h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                    Cancel
                </a>
            </div>
        </form>
    </section>
</section>

@endsection
