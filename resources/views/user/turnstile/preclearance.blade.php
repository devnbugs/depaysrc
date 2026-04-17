@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="mx-auto max-w-lg">
        <div class="rounded-3xl border border-slate-200 bg-white p-10 shadow-sm dark:border-white/10 dark:bg-zinc-950/40">
            <div class="flex items-start gap-4">
                <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-2xl bg-sky-50 text-sky-700 dark:bg-sky-500/10 dark:text-sky-200">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V6l-8-4-8 4v6c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h1 class="text-lg font-semibold text-slate-900 dark:text-white">Verifying your browser</h1>
                    <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-zinc-300">
                        This security check runs invisibly and takes a second.
                    </p>
                    <p id="turnstile-status" class="mt-4 text-xs font-medium text-slate-500 dark:text-zinc-400">
                        Loading security verification…
                    </p>
                </div>
            </div>

            <div id="turnstile-error" class="mt-6 hidden rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900/40 dark:bg-red-950/20 dark:text-red-200"></div>

            <div id="cf-preclearance-turnstile" class="hidden"></div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endpush

@push('script-lib')
    <script>
        (function () {
            const enabled = @json((bool) $isEnabled);
            const siteKey = @json((string) $siteKey);

            const statusEl = document.getElementById('turnstile-status');
            const errorEl = document.getElementById('turnstile-error');
            const mountEl = document.getElementById('cf-preclearance-turnstile');

            if (!enabled || !siteKey) {
                statusEl.textContent = 'Turnstile is not configured. Add CLOUDFLARE_TURNSTILE_SITE_KEY and CLOUDFLARE_TURNSTILE_SECRET_KEY.';
                return;
            }

            function showError(message) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
            }

            async function verifyToken(token) {
                statusEl.textContent = 'Finalizing verification…';

                const response = await fetch(@json(route('turnstile.preclearance.verify')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({token}),
                    credentials: 'same-origin',
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Security verification failed.');
                }

                window.location.href = data.redirect || @json(route('home'));
            }

            function waitForTurnstile(attempts = 0) {
                if (typeof turnstile !== 'undefined') {
                    return renderAndExecute();
                }

                if (attempts > 60) {
                    statusEl.textContent = 'Security verification failed to load. Please refresh.';
                    showError('Turnstile API did not load. Please refresh this page.');
                    return;
                }

                setTimeout(() => waitForTurnstile(attempts + 1), 100);
            }

            function renderAndExecute() {
                statusEl.textContent = 'Running security verification…';

                try {
                    const widgetId = turnstile.render(mountEl, {
                        sitekey: siteKey,
                        theme: 'auto',
                        size: 'invisible',
                        callback: function (token) {
                            verifyToken(token).catch((err) => {
                                statusEl.textContent = 'Verification failed.';
                                showError(err.message || 'Security verification failed.');
                            });
                        },
                        'error-callback': function () {
                            statusEl.textContent = 'Verification failed.';
                            showError('Turnstile returned an error. Please refresh and try again.');
                        },
                        'expired-callback': function () {
                            statusEl.textContent = 'Verification expired. Retrying…';
                            try {
                                turnstile.execute(widgetId);
                            } catch (e) {
                                showError('Verification expired. Please refresh.');
                            }
                        },
                    });

                    turnstile.execute(widgetId);
                } catch (e) {
                    statusEl.textContent = 'Verification failed.';
                    showError('Unable to render the security widget. Please refresh.');
                }
            }

            waitForTurnstile();
        })();
    </script>
@endpush

