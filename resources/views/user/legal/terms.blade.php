@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="mx-auto max-w-4xl space-y-10">
        <header class="space-y-3">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ __($pageTitle ?? 'Terms & Conditions') }}</h1>
            <p class="text-sm text-slate-600 dark:text-zinc-300">
                Last updated: {{ now()->toFormattedDateString() }}
            </p>
        </header>

        <section class="space-y-4 text-sm leading-7 text-slate-700 dark:text-zinc-200">
            <p>
                These Terms & Conditions govern your access to and use of this application. By accessing or using the app, you agree to be bound by these Terms.
                If you do not agree, do not use the app.
            </p>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Eligibility and accounts</h2>
            <ul class="list-disc space-y-2 pl-6 text-sm leading-7 text-slate-700 dark:text-zinc-200">
                <li>You are responsible for maintaining the confidentiality of your account and for all activities under your account.</li>
                <li>You must provide accurate, current, and complete information during registration and onboarding, including when using Google Sign-In.</li>
                <li>We may suspend or terminate accounts involved in fraud, abuse, or policy violations.</li>
            </ul>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Use of services</h2>
            <div class="space-y-3 text-sm leading-7 text-slate-700 dark:text-zinc-200">
                <p>
                    The app may provide wallet funding, bill payments, transfers, and other financial/utility services through third-party providers.
                    Availability, pricing, limits, and processing times may vary.
                </p>
                <ul class="list-disc space-y-2 pl-6">
                    <li>You agree not to use the app for unlawful activity, money laundering, or fraudulent transactions.</li>
                    <li>You authorize us and our providers to process transactions you initiate, subject to validations and security checks.</li>
                    <li>You are responsible for confirming recipient details before submitting transfers or payments.</li>
                </ul>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Google Sign-In</h2>
            <p class="text-sm leading-7 text-slate-700 dark:text-zinc-200">
                If you choose to sign in using Google, you authorize us to use your basic Google profile information (name, email, profile photo, and Google account ID) to create and authenticate your account.
                You can revoke this access at any time in your Google Account settings.
            </p>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Prohibited activities</h2>
            <ul class="list-disc space-y-2 pl-6 text-sm leading-7 text-slate-700 dark:text-zinc-200">
                <li>Attempting to bypass security controls (including anti-bot or rate limit protections).</li>
                <li>Using the app to harass, defraud, or harm others.</li>
                <li>Reverse engineering or interfering with the app’s operation.</li>
            </ul>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Disclaimers</h2>
            <p class="text-sm leading-7 text-slate-700 dark:text-zinc-200">
                The app is provided “as is” and “as available”. We do not guarantee uninterrupted operation or error-free service.
                Third-party provider outages, network issues, and maintenance may affect availability.
            </p>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Limitation of liability</h2>
            <p class="text-sm leading-7 text-slate-700 dark:text-zinc-200">
                To the maximum extent permitted by law, we will not be liable for indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues.
            </p>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Privacy</h2>
            <p class="text-sm leading-7 text-slate-700 dark:text-zinc-200">
                Your use of the app is also governed by our Privacy Policy.
            </p>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Changes</h2>
            <p class="text-sm leading-7 text-slate-700 dark:text-zinc-200">
                We may update these Terms from time to time. Continued use after changes become effective constitutes acceptance of the updated Terms.
            </p>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Contact</h2>
            <p class="text-sm leading-7 text-slate-700 dark:text-zinc-200">
                Questions about these Terms can be sent to
                <span class="font-semibold">{{ config('mail.from.address') ?: 'support@yourdomain.com' }}</span>.
            </p>
        </section>
    </div>
@endsection

