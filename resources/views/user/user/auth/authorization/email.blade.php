@extends($activeTemplate.'layouts.auth')

@section('content')
    <div class="space-y-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-300">
                Email verification
            </div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">@lang('Verify your email address')</h1>
            <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">
                @lang('Enter the code sent to your email to continue to your account dashboard.')
            </p>
        </div>

        <form method="POST" action="{{ route('user.verify.email') }}" class="space-y-5" data-busy-form data-busy-message="Verifying your email code...">
            @csrf

            <div class="space-y-2">
                <label for="email_verified_code" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Verification code')</label>
                <input
                    type="text"
                    name="email_verified_code"
                    id="email_verified_code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="7"
                    placeholder="@lang('Enter 6-digit code')"
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10"
                >
            </div>

            <button type="submit" class="inline-flex h-12 w-full items-center justify-center rounded-full bg-slate-950 px-6 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                @lang('Verify email')
            </button>
        </form>

        <div class="flex flex-wrap items-center justify-between gap-3 rounded-3xl border border-slate-200 bg-slate-50/80 p-4 dark:border-white/10 dark:bg-white/5">
            <p class="text-sm text-slate-600 dark:text-zinc-400">@lang('Didn’t get the code yet?')</p>
            <a href="{{ route('user.send.verify.code', ['type' => 'email']) }}" class="inline-flex h-10 items-center justify-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                @lang('Resend code')
            </a>
        </div>
    </div>
@endsection
