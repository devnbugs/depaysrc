@extends($activeTemplate.'layouts.auth')

@section('content')
    @php
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
    @endphp

    <div class="space-y-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                Get Strated
            </div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">@lang('Join the platform')</h1>
            <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">@lang('Create your account to start using the wallet, bill payment, and savings tools.')</p>
        </div>

        <a href="{{ route('user.google.redirect') }}" class="inline-flex h-11 w-full items-center justify-center gap-3 rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-white/20 dark:hover:bg-zinc-900/70">
            <svg class="h-5 w-5" viewBox="0 0 48 48" aria-hidden="true">
                <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.64 32.657 29.257 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.197l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.236 0-9.605-3.317-11.276-7.946l-6.52 5.022C9.518 39.556 16.227 44 24 44z"/>
                <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.05 12.05 0 0 1-4.084 5.565h.001l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
            </svg>
            Sign up with Google
        </a>

        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200 dark:border-white/10"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-white px-3 text-xs font-medium text-slate-500 dark:bg-zinc-950 dark:text-zinc-400">or</span>
            </div>
        </div>

        <form method="POST" action="{{ route('user.register') }}" id="auth" class="space-y-5" data-busy-form data-busy-message="Creating your account...">
            @csrf

            @isset($reference)
                <input type="hidden" name="referBy" value="{{ $reference }}">
            @endisset

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="firstname" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('First name')</label>
                    <input type="text" name="firstname" id="firstname" value="{{ old('firstname') }}" placeholder="@lang('Enter first name')" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>

                <div class="space-y-2">
                    <label for="lastname" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Last name')</label>
                    <input type="text" name="lastname" id="lastname" value="{{ old('lastname') }}" placeholder="@lang('Enter last name')" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="username" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Username')</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="@lang('Choose a username')" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>

                <div class="space-y-2">
                    <label for="mobile" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Phone number')</label>
                    <input type="tel" name="mobile" id="mobile" value="{{ old('mobile') }}" placeholder="@lang('08123456789')" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>
            </div>

            <div class="space-y-2">
                <label for="email" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Email address')</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="@lang('just@email.com')" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Password')</label>
                    <input type="password" name="password" id="password" placeholder="@lang('Create password')" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Confirm password')</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="@lang('Confirm password')" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>
            </div>

            <div class="space-y-3">
                <label class="flex items-start gap-3 text-sm text-slate-600 dark:text-zinc-300">
                    <!--input type="checkbox" name="agree" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500 dark:border-white/20 dark:bg-zinc-950">
                    <span>@lang('I agree to the terms of service and privacy policy.')</span-->
                    <p class="text-sm text-slate-500 dark:text-zinc-400">
                        @lang('By signing in, you agree to our')
                        <a class="font-semibold text-sky-600 transition hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300" href="{{ route('legal.privacy') }}">@lang('privacy policy')</a>
                        @lang('and')
                        <a class="font-semibold text-sky-600 transition hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300" href="{{ route('legal.terms') }}">@lang('terms')</a>.
                    </p>
                </label>

                <div class="app-submit-actions">
                    <a href="{{ route('user.login') }}" class="text-sm font-medium text-sky-600 transition hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">@lang('Already have an account?')</a>
                    <button type="submit" class="app-submit-button inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                        @lang('Register')
                    </button>
                </div>
            </div>
        </form>
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

@push('script-lib')
    <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush
