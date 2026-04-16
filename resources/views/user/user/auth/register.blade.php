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

            @include('user.partials.turnstile')

            <div class="space-y-3">
                <label class="flex items-start gap-3 text-sm text-slate-600 dark:text-zinc-300">
                    <!--input type="checkbox" name="agree" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500 dark:border-white/20 dark:bg-zinc-950">
                    <span>@lang('I agree to the terms of service and privacy policy.')</span-->
                    <p class="text-sm text-slate-500 dark:text-zinc-400">@lang('By signing in, you agree to our privacy policy and terms.')</p>
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

@push('script-lib')
    <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush
