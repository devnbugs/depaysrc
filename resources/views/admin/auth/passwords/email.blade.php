@extends('admin.layouts.auth')

@section('content')
    <div class="space-y-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">@lang('Forgot Password?')</h1>
            <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">@lang('Enter your email and we will send a verification code for the password reset flow.')</p>
        </div>

        <form action="{{ route('admin.password.reset') }}" method="POST" class="space-y-5" data-busy-form data-busy-message="Sending reset code...">
            @csrf

            <div class="space-y-2">
                <label for="email" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Email')</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="@lang('Enter your email')" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
            </div>

            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('admin.login') }}" class="text-sm font-medium text-sky-600 transition hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">@lang('Back to login')</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    @lang('Send Reset Code')
                </button>
            </div>
        </form>
    </div>
@endsection
