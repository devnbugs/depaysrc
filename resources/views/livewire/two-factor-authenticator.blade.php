<div class="space-y-6">
    @if (session()->has('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-6">
        @if ($isEnabled)
            <!-- 2FA Enabled State -->
            <div class="rounded-3xl border border-emerald-200 bg-emerald-50/60 p-6 dark:border-emerald-500/30 dark:bg-emerald-500/10">
                <div class="flex items-start gap-4">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/20">
                        <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-emerald-950 dark:text-emerald-50">Two-Factor Authentication Active</h4>
                        <p class="mt-1 text-sm leading-6 text-emerald-900 dark:text-emerald-200">
                            Your account is protected with Google Authenticator. You'll need to enter a verification code when signing in.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Disable 2FA Form -->
            <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5 dark:border-white/10 dark:bg-white/5">
                <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Disable 2FA</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                    Enter your current authentication code to disable two-factor authentication on your account.
                </p>

                <form wire:submit="disable" class="mt-4 space-y-4">
                    <div class="space-y-2">
                        <label for="disable-code" class="text-sm font-medium text-slate-700 dark:text-zinc-200">
                            Google Authenticator Code
                        </label>
                        <input
                            id="disable-code"
                            type="text"
                            wire:model="code"
                            inputmode="numeric"
                            maxlength="6"
                            placeholder="000000"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-center text-3xl font-semibold tracking-widest text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10"
                        >
                        @error('code')
                            <p class="text-sm text-rose-600 dark:text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="submit"
                            class="inline-flex h-11 items-center rounded-full bg-rose-600 px-6 text-sm font-semibold text-white transition hover:bg-rose-700 dark:bg-rose-500 dark:hover:bg-rose-600"
                        >
                            Disable 2FA
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- 2FA Disabled State - Setup -->
            <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                <div class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50/70 p-5 dark:border-white/10 dark:bg-white/5">
                    <div>
                        <p class="section-kicker">Setup 2FA</p>
                        <h3 class="mt-3 text-xl font-semibold text-slate-950 dark:text-white">Secure your account</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                            Scan the QR code with your authenticator app or enter the code manually. Then enter the 6-digit code to verify.
                        </p>
                    </div>

                    <div class="space-y-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-white/10 dark:bg-zinc-950/70">
                        <p class="text-xs font-medium uppercase tracking-widest text-slate-500 dark:text-zinc-400">Manual Entry Code</p>
                        <div class="flex items-center gap-2">
                            <input
                                type="text"
                                value="{{ $secret }}"
                                readonly
                                class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 font-mono text-sm text-slate-900 dark:border-white/10 dark:bg-zinc-950/70 dark:text-white"
                            >
                            <button
                                type="button"
                                wire:click="$dispatch('copyToClipboard', { text: '{{ $secret }}' })"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-200 dark:hover:bg-white/5"
                                title="Copy"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">
                            <strong>Important:</strong> Save this code in a secure location. You'll need it if you lose access to your authenticator app.
                        </p>
                    </div>

                    <form wire:submit="enable" class="space-y-4">
                        <div class="space-y-2">
                            <label for="enable-code" class="text-sm font-medium text-slate-700 dark:text-zinc-200">
                                Verification Code
                            </label>
                            <input
                                id="enable-code"
                                type="text"
                                wire:model="code"
                                inputmode="numeric"
                                maxlength="6"
                                placeholder="000000"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-center text-3xl font-semibold tracking-widest text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10"
                            >
                            @error('code')
                                <p class="text-sm text-rose-600 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="inline-flex h-11 items-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600"
                        >
                            Verify & Enable
                        </button>
                    </form>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5 dark:border-white/10 dark:bg-white/5">
                    <p class="section-kicker">Scan QR Code</p>
                    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 dark:border-white/10 dark:bg-zinc-950/70">
                        <div class="flex items-center justify-center">
                            @if ($qrCodeUrl)
                                <img src="{{ $qrCodeUrl }}" alt="2FA QR Code" class="h-72 w-72" />
                            @endif
                        </div>
                        <p class="mt-4 text-center text-sm text-slate-600 dark:text-zinc-400">
                            Scan this code with Google Authenticator, Authy, Microsoft Authenticator, or any compatible TOTP app.
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-6 dark:border-blue-500/30 dark:bg-blue-900/10">
                <h4 class="font-semibold text-blue-950 dark:text-blue-50">Need help?</h4>
                <p class="mt-2 text-sm leading-6 text-blue-900 dark:text-blue-200">
                    Download an authenticator app on your phone. Popular choices include Google Authenticator, Authy, and Microsoft Authenticator.
                </p>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('livewire:navigated', () => {
            document.addEventListener('copyToClipboard', (event) => {
                const text = event.detail.text;
                navigator.clipboard.writeText(text).then(() => {
                    // Show toast or notification
                    console.log('Copied to clipboard');
                }).catch(err => {
                    console.error('Failed to copy:', err);
                });
            });
        });
    </script>
</div>
