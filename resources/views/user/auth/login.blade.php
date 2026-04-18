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

        <a href="{{ route('user.google.redirect') }}" class="inline-flex h-11 w-full items-center justify-center gap-3 rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-white/20 dark:hover:bg-zinc-900/70">
            <svg class="h-5 w-5" viewBox="0 0 48 48" aria-hidden="true">
                <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.64 32.657 29.257 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.197l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.236 0-9.605-3.317-11.276-7.946l-6.52 5.022C9.518 39.556 16.227 44 24 44z"/>
                <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.05 12.05 0 0 1-4.084 5.565h.001l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
            </svg>
            Continue with Google
        </a>

        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200 dark:border-white/10"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-white px-3 text-xs font-medium text-slate-500 dark:bg-zinc-950 dark:text-zinc-400">or</span>
            </div>
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

            <div class="space-y-4">
                <div class="app-submit-actions">
                    <p class="text-sm text-slate-500 dark:text-zinc-400">
                        @lang('By signing in, you agree to our')
                        <a class="font-semibold text-sky-600 transition hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300" href="{{ \Illuminate\Support\Facades\Route::has('legal.privacy') ? route('legal.privacy') : url('/privacy-policy') }}">@lang('privacy policy')</a>
                        @lang('and')
                        <a class="font-semibold text-sky-600 transition hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300" href="{{ \Illuminate\Support\Facades\Route::has('legal.terms') ? route('legal.terms') : url('/terms') }}">@lang('terms')</a>.
                    </p>
                    <button type="submit" class="app-submit-button inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                        @lang('Login')
                    </button>
                </div>
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

@if(config('services.countly.debug'))
    <script>
        window.CountlyDebug = true;
        window.countlyLog = function(...args) {
            if (window.CountlyDebug) {
                console.log('[Countly]', ...args);
            }
        };
    </script>
@endif
