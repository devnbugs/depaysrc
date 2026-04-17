@extends($activeTemplate.'layouts.auth')

@section('content')
    <div class="space-y-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                Extra verification
            </div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">@lang('Enter your authenticator code')</h1>
            <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">
                @lang('Open your authenticator app and enter the current 6-digit code to finish signing in.')
            </p>
        </div>


        <form method="POST" action="{{ route('user.go2fa.verify') }}" class="space-y-5" data-busy-form data-busy-message="Verifying your authenticator code...">
            @csrf

            @include('user.partials.turnstile')

            <div class="space-y-2">
                <label for="code" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Authenticator code')</label>
                <input
                    type="text"
                    name="code"
                    id="code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="7"
                    placeholder="@lang('Enter 6-digit code')"
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10"
                >
            </div>

            <button type="submit" class="inline-flex h-12 w-full items-center justify-center rounded-full bg-slate-950 px-6 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                @lang('Verify and continue')
            </button>
        </form>

        <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-4 text-sm leading-6 text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">
            @lang('If your code is not working, make sure your device time is set automatically and try the newest code shown in your authenticator app.')
        </div>
    </div>
@endsection
