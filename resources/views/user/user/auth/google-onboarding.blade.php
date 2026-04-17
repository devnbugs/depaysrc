@extends($activeTemplate.'layouts.auth')

@section('content')
    <div class="space-y-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-slate-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200">
                Google Sign-in
            </div>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">Finish setting up your account</h1>
            <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">
                We’ve imported your email from Google. Please add the remaining details to complete signup.
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-4 dark:border-white/10 dark:bg-white/5">
            <div class="flex items-center gap-3">
                @if (!empty($profile['avatar']))
                    <img src="{{ $profile['avatar'] }}" alt="Google profile" class="h-11 w-11 rounded-2xl border border-slate-200 object-cover dark:border-white/10">
                @else
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                @endif
                <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $profile['name'] ?: 'Google user' }}</p>
                    <p class="text-sm text-slate-600 dark:text-zinc-300">{{ $profile['email'] }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('user.google.onboarding.complete') }}" class="space-y-5" data-busy-form data-busy-message="Finishing setup...">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="firstname" class="text-sm font-medium text-slate-700 dark:text-zinc-200">First name</label>
                    <input type="text" name="firstname" id="firstname" value="{{ old('firstname', $firstname) }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>

                <div class="space-y-2">
                    <label for="lastname" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Last name</label>
                    <input type="text" name="lastname" id="lastname" value="{{ old('lastname', $lastname) }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="username" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username', $suggestedUsername) }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>

                <div class="space-y-2">
                    <label for="mobile" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Phone number</label>
                    <input type="tel" name="mobile" id="mobile" value="{{ old('mobile') }}" placeholder="+2348012345678" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>
            </div>

            <div class="app-submit-actions">
                <p class="text-sm text-slate-500 dark:text-zinc-400">
                    By continuing, you agree to our
                    <a class="font-semibold text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300" href="{{ \Illuminate\Support\Facades\Route::has('legal.privacy') ? route('legal.privacy') : url('/privacy-policy') }}">Privacy Policy</a>
                    and
                    <a class="font-semibold text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300" href="{{ \Illuminate\Support\Facades\Route::has('legal.terms') ? route('legal.terms') : url('/terms') }}">Terms</a>.
                </p>
                <button type="submit" class="app-submit-button inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    Complete signup
                </button>
            </div>
        </form>
    </div>
@endsection
