@extends($activeTemplate.'layouts.dashboard')

@section('content')
<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="space-y-2">
            <p class="section-kicker">Security</p>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                Change your password
            </h1>
            <p class="max-w-2xl text-sm leading-6 text-slate-600 dark:text-zinc-300">
                Update your sign-in password with a stronger replacement whenever you want to refresh account security.
            </p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="panel-card p-6 sm:p-8">
            <div class="mb-6 space-y-2">
                <h2 class="section-title text-xl">Password update</h2>
                <p class="text-sm text-slate-500 dark:text-zinc-400">
                    Use a password you do not reuse elsewhere and keep it memorable to you alone.
                </p>
            </div>

            <form action="" method="post" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="current_password" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Old password</label>
                    <input id="current_password" type="password" placeholder="Old password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10" name="current_password" required autocomplete="current-password">
                </div>

                <div class="space-y-2">
                    <label for="new_password" class="text-sm font-medium text-slate-700 dark:text-zinc-200">New password</label>
                    <input id="new_password" type="password" placeholder="New password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10" name="password" required autocomplete="new-password">
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Confirm new password</label>
                    <input id="password_confirmation" type="password" placeholder="Confirm new password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10" name="password_confirmation" required autocomplete="new-password">
                </div>

                <button type="submit" class="app-submit-button inline-flex h-12 rounded-full bg-slate-950 px-6 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    Save changes
                </button>
            </form>
        </div>

        <div class="space-y-4">
            <div class="panel-card p-6">
                <p class="section-kicker">Guidance</p>
                <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600 dark:text-zinc-300">
                    <p>Make the new password longer than the old one if possible.</p>
                    <p>Avoid recycled passwords from email, banking, or social accounts.</p>
                    <p>After updating, sign out on shared devices if you used them recently.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
