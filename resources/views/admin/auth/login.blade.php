@extends('admin.layouts.auth')

@section('content')
    <div class="space-y-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-300">
                Admin sign in
            </div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">@lang('Welcome back')</h1>
            <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">@lang('Use your admin credentials to continue into the management dashboard.')</p>
        </div>

        <form action="{{ url('/admin') }}" method="POST" class="space-y-5" data-busy-form data-busy-message="Signing in to admin panel...">
            @csrf

            <div class="space-y-2">
                <label for="username" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Username / Access Name')</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="@lang('Enter your username')" required autofocus class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
            </div>

            <div class="space-y-2">
                <label for="password" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Password / PassPhrase')</label>
                <input type="password" name="password" id="password" placeholder="@lang('Enter your password')" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
            </div>

            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('admin.password.reset') }}" class="text-sm font-medium text-sky-600 transition hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">@lang('Forgot password?')</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    @lang('Login')
                </button>
            </div>
        </form>
    </div>
@endsection
