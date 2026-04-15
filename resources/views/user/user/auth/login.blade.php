@extends($activeTemplate.'layouts.auth')

@section('content')
    <div class="space-y-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-300">
                Secured by Cloudflare
            </div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">@lang('Login to continue')</h1>
            <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">@lang('Sign in to manage your wallet, deposits, bills, and transfer history.')</p>
        </div>

        <form method="POST" action="{{ route('user.login') }}" id="auth" class="space-y-5" data-busy-form data-busy-message="Signing you in...">
            @csrf

            <div class="space-y-2">
                <label for="username" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Email or username')</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="@lang('Enter your email or username')" required autofocus class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between gap-3">
                    <label for="password" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Password')</label>
                    <a href="{{ route('user.password.request') }}" class="text-sm font-medium text-sky-600 transition hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">@lang('Forgot password?')</a>
                </div>
                <input type="password" name="password" id="password" placeholder="@lang('Enter your password')" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
            </div>

            @include('user.partials.turnstile')

            <div class="flex items-center justify-between gap-3">
                <p class="text-sm text-slate-500 dark:text-zinc-400">@lang('By signing in, you agree to our privacy policy and terms.')</p>
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    @lang('Login')
                </button>
            </div>
        </form>

        <div class="space-y-3 rounded-3xl border border-slate-200 bg-slate-50/80 p-4 dark:border-white/10 dark:bg-white/5">
            <div class="space-y-1">
                <p class="text-sm font-semibold text-slate-900 dark:text-white">@lang('Use a passkey instead')</p>
                <p class="text-sm text-slate-600 dark:text-zinc-400">@lang('Sign in with Face ID, Touch ID, Windows Hello, or your security key.')</p>
            </div>

            <x-authenticate-passkey>
                <button type="button" class="inline-flex h-11 w-full items-center justify-center rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                    @lang('Login with Passkey')
                </button>
            </x-authenticate-passkey>
        </div>

        <p class="text-sm text-slate-600 dark:text-zinc-300">
            @lang('New user?')
            <a href="{{ route('user.register') }}" class="font-semibold text-sky-600 transition hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">@lang('Create an account')</a>
        </p>
    </div>
@endsection
