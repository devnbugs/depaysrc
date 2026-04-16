@extends($activeTemplate.'layouts.auth')

@section('content')
    <div class="space-y-6">
        <div class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">@lang('Reset Password')</h1>
            <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">@lang('Enter your email or username and we will send a password reset code.')</p>
        </div>

        <form method="POST" action="{{ route('user.password.email') }}" class="space-y-5" data-busy-form data-busy-message="Sending password reset code...">
            @csrf

            <div class="space-y-2">
                <label for="type" class="text-sm font-medium text-slate-700 dark:text-zinc-200">@lang('Select one')</label>
                <select name="type" id="type" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                    <option value="email">@lang('E-Mail Address')</option>
                    <option value="username">@lang('Username')</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="my_value text-sm font-medium text-slate-700 dark:text-zinc-200"></label>
                <input type="text" name="value" value="{{ old('value') }}" required autofocus class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
            </div>

            @include('user.partials.turnstile')

            <div class="app-submit-actions">
                <a href="{{ route('user.login') }}" class="text-sm font-medium text-sky-600 transition hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">@lang('Back to login')</a>
                <button type="submit" class="app-submit-button inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    @lang('Send Code')
                </button>
            </div>
        </form>
    </div>
@endsection

@push('script')
<script>
    (function ($) {
        "use strict";
        function syncLabel() {
            $('.my_value').text($('select[name=type] :selected').text());
        }

        syncLabel();
        $('select[name=type]').on('change', syncLabel);
    })(jQuery);
</script>
@endpush
