<div class="invisible-turnstile-container" x-data="invisibleTurnstile()" x-init="initTurnstile()">
    <!-- Hidden Turnstile Widget - Rendered Invisibly -->
    <div
        id="cf-invisible-turnstile"
        wire:ignore
        class="hidden"
        data-sitekey="{{ $siteKey }}"
        data-theme="dark"
        data-size="invisible"
        data-execution="execute"
        data-language="auto"
        data-tabindex="0"
        data-callback="onInvisibleTurnstileSuccess"
        data-error-callback="onInvisibleTurnstileError"
        data-expired-callback="onInvisibleTurnstileExpired"
        data-timeout-callback="onInvisibleTurnstileTimeout"
        data-retry="auto"
        data-retry-interval="5000"
        data-refresh-expired="auto"
        data-refresh-timeout="auto"
        data-response-field="false"
        data-feedback-enabled="true"
    ></div>

    <!-- Hidden Input for Token Storage -->
    <input type="hidden" id="invisible-turnstile-token" name="invisible-turnstile-token" value="">

    <!-- Loading Indicator -->
    <div id="turnstile-loading" class="hidden fixed bottom-4 right-4 flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-lg shadow-lg z-50">
        <div class="animate-spin">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <span class="text-sm">Security verification in progress...</span>
    </div>

    <!-- Security Alert -->
    <div id="security-alert" class="hidden fixed top-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg max-w-sm z-50 dark:bg-red-950/20 dark:border-red-900/30">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
                <h3 class="font-semibold text-red-900 dark:text-red-300">Security Alert</h3>
                <p id="alert-message" class="text-sm text-red-700 dark:text-red-400 mt-1">Unusual activity detected</p>
            </div>
        </div>
    </div>

    <!-- Inline Turnstile Widget -->
    <div id="inline-turnstile-container" class="hidden mt-4 p-4 border border-slate-200 rounded-lg bg-slate-50 dark:border-white/10 dark:bg-white/5">
        <p class="text-sm font-medium text-slate-700 dark:text-zinc-200 mb-3">
            Additional security verification required:
        </p>
        <div id="cf-inline-turnstile"></div>
    </div>

    @if (! $isEnabled)
        <div class="hidden text-xs text-slate-500 dark:text-zinc-400">
            Turnstile security widget is not configured. Please add CLOUDFLARE_TURNSTILE_SITE_KEY to .env
        </div>
    @endif

    <style>
        .invisible-turnstile-container {
            position: relative;
            min-height: 0;
        }

        #cf-invisible-turnstile {
            display: none !important;
            position: fixed;
            left: -9999px;
            top: -9999px;
            width: 1px;
            height: 1px;
            visibility: hidden;
            pointer-events: none;
        }

        #security-alert {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    @once
        @push('script-lib')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endpush
    @endonce

    @if ($isEnabled)
        @push('script-lib')
            <script>
                function invisibleTurnstile() {
                    return {
                        token: '',
                        isProcessing: false,
                        actionQueue: [],
                        suspiciousCount: 0,
                        widgetId: null,
                        inlineWidgetId: null,

                        initTurnstile() {
                            console.log('🔐 Invisible Turnstile initializing...');

                            if (typeof this.$wire !== 'undefined') {
                                this.$wire.initializeWidget();
                            }

                            this.waitForTurnstileAndRender();
                        },

                        waitForTurnstileAndRender() {
                            let attempts = 0;
                            const maxAttempts = 50;

                            const interval = setInterval(() => {
                                attempts++;

                                if (typeof turnstile !== 'undefined') {
                                    clearInterval(interval);
                                    this.renderTurnstile();
                                }

                                if (attempts >= maxAttempts) {
                                    clearInterval(interval);
                                    console.warn('⚠ Turnstile API not loaded after waiting');
                                }
                            }, 100);
                        },

                        renderTurnstile() {
                            if (typeof turnstile === 'undefined') {
                                console.warn('⚠ Turnstile API not loaded yet');
                                return;
                            }

                            const container = document.getElementById('cf-invisible-turnstile');

                            if (!container) {
                                console.warn('⚠ Invisible Turnstile container not found');
                                return;
                            }

                            if (this.widgetId !== null) {
                                return;
                            }

                            this.widgetId = turnstile.render('#cf-invisible-turnstile', {
                                sitekey: '{{ $siteKey }}',
                                theme: 'dark',
                                size: 'invisible',
                                callback: (token) => this.onTokenReceived(token),
                                'error-callback': () => this.onTurnstileError(),
                                'expired-callback': () => this.onTurnstileExpired(),
                                'timeout-callback': () => this.onTurnstileTimeout(),
                                language: 'auto',
                                retry: 'auto',
                                'retry-interval': 5000,
                                'response-field': false,
                                'response-field-name': 'invisible-turnstile-token'
                            });

                            console.log('✓ Invisible Turnstile rendered with ID:', this.widgetId);
                        },

                        executeProtectedAction(actionType, actionData = {}) {
                            console.log(`🔒 Executing protected action: ${actionType}`);

                            if (this.isProcessing) {
                                console.warn('⚠ Action already processing - queuing request');
                                this.actionQueue.push({ actionType, actionData });
                                return false;
                            }

                            this.isProcessing = true;
                            this.showLoadingIndicator();

                            if (typeof this.$wire === 'undefined') {
                                console.error('✗ Livewire $wire unavailable');
                                this.showSecurityAlert('Security verification unavailable. Please refresh and try again.');
                                this.isProcessing = false;
                                this.hideLoadingIndicator();
                                return false;
                            }

                            this.$wire.detectMultipleRequests(actionType)
                                .then((allowed) => {
                                    if (!allowed) {
                                        this.showSecurityAlert('⛔ Too many requests. Please wait before trying again.', 'error');
                                        this.isProcessing = false;
                                        this.hideLoadingIndicator();
                                        return;
                                    }

                                    if (typeof turnstile !== 'undefined' && this.widgetId !== null) {
                                        turnstile.execute(this.widgetId);
                                    } else {
                                        console.error('✗ Turnstile widget not ready');
                                        this.showSecurityAlert('Security verification unavailable. Please try again.');
                                        this.isProcessing = false;
                                        this.hideLoadingIndicator();
                                    }
                                })
                                .catch((error) => {
                                    console.error('✗ detectMultipleRequests failed:', error);
                                    this.showSecurityAlert('Unable to validate request. Please try again.');
                                    this.isProcessing = false;
                                    this.hideLoadingIndicator();
                                });

                            return true;
                        },

                        onTokenReceived(token) {
                            if (!token || token.length === 0) {
                                console.error('✗ Empty token received');
                                this.isProcessing = false;
                                this.hideLoadingIndicator();
                                return;
                            }

                            this.token = token;
                            console.log('✓ Turnstile token received, length:', token.length);

                            const tokenInput = document.getElementById('invisible-turnstile-token');
                            if (tokenInput) {
                                tokenInput.value = token;
                            }

                            if (typeof this.$wire !== 'undefined') {
                                this.$wire.call('handleToken', token);
                            }

                            if (this.actionQueue.length > 0) {
                                const { actionType, actionData } = this.actionQueue.shift();
                                this.isProcessing = false;
                                this.hideLoadingIndicator();

                                setTimeout(() => {
                                    this.executeProtectedAction(actionType, actionData);
                                }, 200);
                            } else {
                                this.isProcessing = false;
                                this.hideLoadingIndicator();
                            }
                        },

                        verifySensitiveAction(action, data = {}) {
                            if (typeof this.$wire === 'undefined') {
                                this.showSecurityAlert('Livewire verification is unavailable.');
                                return false;
                            }

                            return this.$wire.protectSensitiveRequest(action, data)
                                .then((allowed) => {
                                    if (!allowed) {
                                        this.showSecurityAlert(`❌ Cannot process ${action} at this time. Please try again later.`);
                                        return false;
                                    }

                                    return this.$wire.verifyAction(action, this.token);
                                })
                                .catch((error) => {
                                    console.error('✗ verifySensitiveAction failed:', error);
                                    this.showSecurityAlert(`❌ Cannot process ${action} right now.`);
                                    return false;
                                });
                        },

                        showLoadingIndicator() {
                            const loader = document.getElementById('turnstile-loading');
                            if (loader) {
                                loader.classList.remove('hidden');
                            }
                        },

                        hideLoadingIndicator() {
                            const loader = document.getElementById('turnstile-loading');
                            if (loader) {
                                loader.classList.add('hidden');
                            }
                        },

                        showSecurityAlert(message, type = 'warning') {
                            const alert = document.getElementById('security-alert');
                            const alertMessage = document.getElementById('alert-message');

                            if (alert && alertMessage) {
                                alertMessage.textContent = message;
                                alert.classList.remove('hidden');

                                setTimeout(() => {
                                    alert.classList.add('hidden');
                                }, 5000);
                            }

                            this.suspiciousCount++;
                        },

                        showInlineTurnstile() {
                            const container = document.getElementById('inline-turnstile-container');
                            const inlineTarget = document.getElementById('cf-inline-turnstile');

                            if (!container || !inlineTarget || typeof turnstile === 'undefined') {
                                return;
                            }

                            container.classList.remove('hidden');

                            if (this.inlineWidgetId === null) {
                                this.inlineWidgetId = turnstile.render('#cf-inline-turnstile', {
                                    sitekey: '{{ $siteKey }}',
                                    callback: () => this.onInlineTurnstileComplete(),
                                });
                            }
                        },

                        hideInlineTurnstile() {
                            const container = document.getElementById('inline-turnstile-container');
                            if (container) {
                                container.classList.add('hidden');
                            }
                        },

                        onInlineTurnstileComplete() {
                            console.log('✓ Inline Turnstile completed');
                            this.hideInlineTurnstile();
                            this.$dispatch('turnstile-verified');
                        },

                        onTurnstileError() {
                            console.error('✗ Turnstile error');

                            const tokenInput = document.getElementById('invisible-turnstile-token');
                            if (tokenInput) {
                                tokenInput.value = '';
                            }

                            this.token = '';
                            this.isProcessing = false;
                            this.hideLoadingIndicator();
                            this.showSecurityAlert('Security verification failed. Please try again.');
                        },

                        onTurnstileExpired() {
                            console.warn('⚠ Turnstile token expired');

                            const tokenInput = document.getElementById('invisible-turnstile-token');
                            if (tokenInput) {
                                tokenInput.value = '';
                            }

                            this.token = '';
                        },

                        onTurnstileTimeout() {
                            console.warn('⚠ Turnstile timeout - will retry');
                        }
                    };
                }

                function onInvisibleTurnstileSuccess(token) {
                    console.log('✓ Invisible Turnstile successful, token length:', token.length);

                    const tokenInput = document.getElementById('invisible-turnstile-token');
                    if (tokenInput) {
                        tokenInput.value = token;
                    }

                    const container = document.querySelector('.invisible-turnstile-container');
                    if (container && container.__x && container.__x.$data) {
                        container.__x.$data.onTokenReceived(token);
                    } else {
                        console.warn('⚠ Alpine component not found or not initialized');
                    }
                }

                function onInvisibleTurnstileError(errorData) {
                    console.error('✗ Invisible Turnstile error:', errorData);

                    const tokenInput = document.getElementById('invisible-turnstile-token');
                    if (tokenInput) {
                        tokenInput.value = '';
                    }

                    const container = document.querySelector('.invisible-turnstile-container');
                    if (container && container.__x && container.__x.$data) {
                        container.__x.$data.onTurnstileError();
                    }
                }

                function onInvisibleTurnstileExpired() {
                    console.warn('⚠ Invisible Turnstile token expired');

                    const tokenInput = document.getElementById('invisible-turnstile-token');
                    if (tokenInput) {
                        tokenInput.value = '';
                    }

                    const container = document.querySelector('.invisible-turnstile-container');
                    if (container && container.__x && container.__x.$data) {
                        container.__x.$data.onTurnstileExpired();
                    }
                }

                function onInvisibleTurnstileTimeout() {
                    console.warn('⚠ Invisible Turnstile timeout - will retry');

                    const container = document.querySelector('.invisible-turnstile-container');
                    if (container && container.__x && container.__x.$data) {
                        container.__x.$data.onTurnstileTimeout();
                    }
                }

                document.addEventListener('livewire:navigated', () => {
                    const container = document.querySelector('.invisible-turnstile-container');
                    if (container && container.__x && container.__x.$data) {
                        container.__x.$data.waitForTurnstileAndRender();
                    }
                });

                document.addEventListener('dispatch-invisible-turnstile', function () {
                    const container = document.querySelector('.invisible-turnstile-container');
                    if (container && container.__x && container.__x.$data) {
                        container.__x.$data.executeProtectedAction('manual_execution');
                    }
                });
            </script>
        @endpush
    @endif
</div>