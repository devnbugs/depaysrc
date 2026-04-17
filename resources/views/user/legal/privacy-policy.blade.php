@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="mx-auto max-w-4xl space-y-10">
        <header class="space-y-3">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ __($pageTitle ?? 'Privacy Policy') }}</h1>
            <p class="text-sm text-slate-600 dark:text-zinc-300">
                Last updated: {{ now()->toFormattedDateString() }}
            </p>
        </header>

        <section class="space-y-4 text-sm leading-7 text-slate-700 dark:text-zinc-200">
            <p>
                This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use this application and related services.
                If you do not agree with the terms of this Privacy Policy, please do not access the app.
            </p>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Information we collect</h2>
            <div class="space-y-3 text-sm leading-7 text-slate-700 dark:text-zinc-200">
                <p><span class="font-semibold">Account information:</span> name, email address, phone number, username, and other profile details you provide.</p>
                <p><span class="font-semibold">Google Sign-In:</span> when you choose “Continue with Google”, we receive basic profile information such as your Google account ID, name, email address, and profile photo (if available). We use this only to create/sign you into your account.</p>
                <p><span class="font-semibold">Transaction and service data:</span> details needed to provide wallet, bills, deposits, transfers, identity verification, and related services (when you use them).</p>
                <p><span class="font-semibold">Device and usage data:</span> IP address, browser/device details, log data, and security/fraud signals.</p>
                <p><span class="font-semibold">Cookies and similar technologies:</span> used to maintain sessions, improve performance, and protect the app.</p>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">How we use your information</h2>
            <ul class="list-disc space-y-2 pl-6 text-sm leading-7 text-slate-700 dark:text-zinc-200">
                <li>To create and manage accounts, including login with Google.</li>
                <li>To provide and maintain services (wallet, bills, deposits, transfers, etc.).</li>
                <li>To detect, prevent, and respond to fraud, abuse, and security incidents.</li>
                <li>To comply with legal obligations, KYC/AML, and regulatory requirements (where applicable).</li>
                <li>To communicate service updates, security alerts, and support responses.</li>
            </ul>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">How we share your information</h2>
            <div class="space-y-3 text-sm leading-7 text-slate-700 dark:text-zinc-200">
                <p>We may share information with:</p>
                <ul class="list-disc space-y-2 pl-6">
                    <li><span class="font-semibold">Service providers</span> that help us operate the app (hosting, email/SMS, security, analytics, payment and billing providers).</li>
                    <li><span class="font-semibold">Regulators and law enforcement</span> when required by law or to protect our rights and users.</li>
                    <li><span class="font-semibold">Business transfers</span> if we are involved in a merger, acquisition, or asset sale (subject to applicable laws).</li>
                </ul>
                <p>We do not sell your personal information.</p>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Google API Services disclosure</h2>
            <div class="space-y-3 text-sm leading-7 text-slate-700 dark:text-zinc-200">
                <p>
                    This app may use Google OAuth to authenticate you. We request only the minimum scopes required for authentication (basic profile and email).
                    We do not access or store your Google password.
                </p>
                <p>
                    You can revoke the app’s access at any time from your Google Account permissions.
                </p>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Data retention</h2>
            <p class="text-sm leading-7 text-slate-700 dark:text-zinc-200">
                We retain information as long as necessary to provide services and comply with legal obligations. Retention periods may vary depending on the type of data and applicable requirements.
            </p>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Your choices</h2>
            <ul class="list-disc space-y-2 pl-6 text-sm leading-7 text-slate-700 dark:text-zinc-200">
                <li>Update your profile information from your account settings (where available).</li>
                <li>Revoke Google access in your Google Account settings.</li>
                <li>Request account deletion by contacting support (subject to legal retention requirements).</li>
            </ul>
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Contact</h2>
            <p class="text-sm leading-7 text-slate-700 dark:text-zinc-200">
                If you have questions about this Privacy Policy, contact us at
                <span class="font-semibold">{{ config('mail.from.address') ?: 'support@yourdomain.com' }}</span>.
            </p>
        </section>
    </div>
@endsection

