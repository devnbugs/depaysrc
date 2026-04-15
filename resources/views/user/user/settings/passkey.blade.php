@extends($activeTemplate.'layouts.dashboard')

@section('content')
<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="space-y-4">
            <div class="flex flex-wrap gap-2">
                <span class="section-kicker">Security</span>
                <span class="section-kicker">Passkeys</span>
            </div>

            <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                Manage your passkeys
            </h2>
            <p class="max-w-3xl section-copy">
                Register biometric or hardware-backed passkeys for faster sign-in and safer account approvals. This page now uses the Spatie WebAuthn passkey engine.
            </p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <article class="panel-card p-5">
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Registered passkeys</p>
            <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-white">{{ $user->passkeys->count() }}</p>
        </article>
        <article class="panel-card p-5">
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Login method</p>
            <p class="mt-3 text-xl font-semibold text-slate-950 dark:text-white">Passkey ready</p>
            <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">Use the new button on the login page anytime.</p>
        </article>
        <article class="panel-card p-5">
            <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Security rule</p>
            <p class="mt-3 text-xl font-semibold text-slate-950 dark:text-white">PIN or 2FA stays required</p>
            <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">If you remove all passkeys, keep another approval method enabled.</p>
        </article>
    </div>

    <section class="panel-card p-6">
        <livewire:passkey-manager />
    </section>

    <section class="rounded-2xl border border-blue-200 bg-blue-50/60 p-6 dark:border-blue-500/30 dark:bg-blue-900/10">
        <h3 class="text-lg font-semibold text-blue-950 dark:text-blue-50">Supported devices</h3>
        <p class="mt-2 text-sm leading-6 text-blue-900 dark:text-blue-200">
            Passkeys work with Face ID, Touch ID, Windows Hello, Android biometrics, password managers that sync passkeys, and compatible hardware security keys.
        </p>
    </section>
</section>
@endsection
