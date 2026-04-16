@extends($activeTemplate.'layouts.auth')

@section('content')
    <div class="space-y-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">@lang('Reset Password')</h1>
            <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">@lang('Choose a new password to restore access to your account.')</p>
        </div>

        <form method="POST" action="{{ route('user.password.update') }}" class="space-y-5" data-busy-form data-busy-message="Updating your password...">
            @csrf

            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="space-y-2">
                <label for="password" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Password')</label>
                <input type="password" name="password" id="password" placeholder="@lang('Enter new password')" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
            </div>

            <div class="space-y-2">
                <label for="password_confirmation" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Confirm password')</label>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="@lang('Confirm new password')" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
            </div>

            @include('user.partials.turnstile')

            <div class="app-submit-actions">
                <a href="{{ route('user.login') }}" class="text-sm font-medium text-sky-600 transition hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">@lang('Back to login')</a>
                <button type="submit" class="app-submit-button inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    @lang('Reset Password')
                </button>
            </div>
        </form>
    </div>
@endsection
