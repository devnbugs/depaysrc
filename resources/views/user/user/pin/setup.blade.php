@extends($activeTemplate.'layouts.dashboard')

@section('content')

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div>
            <div class="flex flex-wrap gap-2">
                <span class="section-kicker">Security</span>
                <span class="section-kicker">PIN Setup</span>
            </div>

            <div class="space-y-4">
                <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Set up your PIN
                </h2>
                <p class="max-w-2xl section-copy">
                    Create a secure 4-digit PIN that will be required for transactions and payments. Keep it confidential and memorable.
                </p>
            </div>
        </div>
    </div>

    <section class="panel-card p-6 max-w-2xl">
        <form action="{{ route('user.pin.set') }}" method="post" class="space-y-6">
            @csrf

            <div class="rounded-2xl border border-blue-200 bg-blue-50/50 p-6 dark:border-blue-500/30 dark:bg-blue-900/10">
                <h4 class="font-semibold text-blue-950 dark:text-blue-50 mb-3">PIN Requirements</h4>
                <ul class="space-y-2 text-sm text-blue-900 dark:text-blue-200">
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Must be exactly 4 digits (0-9)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Cannot be all same digits (1111, 2222, etc.)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Should be easy for you to remember but hard to guess</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Never share your PIN with anyone</span>
                    </li>
                </ul>
            </div>

            <div class="space-y-2">
                <label for="pin" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                    Create PIN <span class="text-rose-600">*</span>
                </label>
                <input 
                    type="password"
                    name="pin"
                    id="pin"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500"
                    placeholder="Enter 4-digit PIN"
                    maxlength="4"
                    inputmode="numeric"
                    pattern="[0-9]{4}"
                    required
                    @error('pin') aria-invalid="true" @enderror
                />
                @error('pin')
                    <p class="text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="verify_pin" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                    Confirm PIN <span class="text-rose-600">*</span>
                </label>
                <input 
                    type="password"
                    name="verify_pin"
                    id="verify_pin"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500"
                    placeholder="Re-enter your PIN"
                    maxlength="4"
                    inputmode="numeric"
                    pattern="[0-9]{4}"
                    required
                    @error('verify_pin') aria-invalid="true" @enderror
                />
                @error('verify_pin')
                    <p class="text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="app-action-row border-t border-slate-200 pt-6 dark:border-white/10">
                <button type="submit" class="app-submit-button inline-flex h-11 rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                    Create PIN
                </button>
                <a href="{{ route('user.user.pin.index') }}" class="inline-flex h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                    Cancel
                </a>
            </div>
        </form>
    </section>
</section>

@endsection
