<div class="invisible-turnstile-container" wire:ignore x-data="invisibleTurnstile()" @ready="initTurnstile()">
    <!-- Hidden Turnstile Widget - Rendered Invisibly -->
    <div id="cf-invisible-turnstile" 
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
         data-feedback-enabled="true">
    </div>

    <!-- Hidden Input for Token Storage -->
    <input type="hidden" id="invisible-turnstile-token" name="invisible-turnstile-token" value="">

    <!-- Loading Indicator (Optional - Hidden by default) -->
    <div id="turnstile-loading" class="hidden fixed bottom-4 right-4 flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-lg shadow-lg">
        <div class="animate-spin">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <span class="text-sm">Security verification in progress...</span>
    </div>

    <!-- Security Alert (Shown when suspicious activity detected) -->
    <div id="security-alert" class="hidden fixed top-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg max-w-sm dark:bg-red-950/20 dark:border-red-900/30">
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

    <!-- Inline Turnstile Widget (Shown when needed) -->
    <div id="inline-turnstile-container" class="hidden mt-4 p-4 border border-slate-200 rounded-lg bg-slate-50 dark:border-white/10 dark:bg-white/5">
        <p class="text-sm font-medium text-slate-700 dark:text-zinc-200 mb-3">Additional security verification required:</p>
        <div id="cf-inline-turnstile"></div>
    </div>

    @if($isEnabled)
        <script>
            function invisibleTurnstile() {
                return {
                    token: '',
                    isProcessing: false,
                    actionQueue: [],
                    suspiciousCount: 0,

                    /**
                     * Initialize Turnstile widget
                     */
                    initTurnstile() {
                        @this.initializeWidget();
                        console.log('🔐 Invisible Turnstile initialized');
                    },

                    /**
                     * Execute action with Turnstile protection
                     * Usage: x-on:execute-protected-action="executeProtectedAction('payment', data)"
                     */
                    executeProtectedAction(actionType, actionData = {}) {
                        console.log(`🔒 Executing protected action: ${actionType}`);
                        
                        if (this.isProcessing) {
                            console.warn('⚠ Action already processing - queuing request');
                            this.actionQueue.push({ actionType, actionData });
                            return false;
                        }

                        this.isProcessing = true;
                        this.showLoadingIndicator();

                        // Check for multiple requests
                        if (!@this.detectMultipleRequests(actionType)) {
                            this.showSecurityAlert('⛔ Too many requests. Please wait before trying again.', 'error');
                            this.isProcessing = false;
                            this.hideLoadingIndicator();
                            return false;
                        }

                        // Execute Turnstile
                        grecaptcha.execute();
                    },

                    /**
                     * Handle successful Turnstile verification
                     */
                    onTokenReceived(token) {
                        this.token = token;
                        console.log('✓ Turnstile token received');
                        
                        @this.call('handleToken', token);

                        // Process queued actions
                        if (this.actionQueue.length > 0) {
                            const { actionType, actionData } = this.actionQueue.shift();
                            this.executeProtectedAction(actionType, actionData);
                        } else {
                            this.isProcessing = false;
                            this.hideLoadingIndicator();
                        }
                    },

                    /**
                     * Verify specific sensitive action
                     * Used for payments, transfers, etc.
                     */
                    verifySensitiveAction(action, data = {}) {
                        if (!@this.protectSensitiveRequest(action, data)) {
                            this.showSecurityAlert(`❌ Cannot process ${action} at this time. Please try again later.`);
                            return false;
                        }

                        return @this.verifyAction(action, this.token);
                    },

                    /**
                     * Show loading indicator
                     */
                    showLoadingIndicator() {
                        const loader = document.getElementById('turnstile-loading');
                        if (loader) loader.classList.remove('hidden');
                    },

                    /**
                     * Hide loading indicator
                     */
                    hideLoadingIndicator() {
                        const loader = document.getElementById('turnstile-loading');
                        if (loader) loader.classList.add('hidden');
                    },

                    /**
                     * Show security alert
                     */
                    showSecurityAlert(message, type = 'warning') {
                        const alert = document.getElementById('security-alert');
                        const alertMessage = document.getElementById('alert-message');
                        
                        if (alert && alertMessage) {
                            alertMessage.textContent = message;
                            alert.classList.remove('hidden');
                            
                            // Auto-hide after 5 seconds
                            setTimeout(() => {
                                alert.classList.add('hidden');
                            }, 5000);
                        }

                        this.suspiciousCount++;
                    },

                    /**
                     * Show inline Turnstile for additional verification
                     */
                    showInlineTurnstile() {
                        const container = document.getElementById('inline-turnstile-container');
                        if (container) {
                            container.classList.remove('hidden');
                            grecaptcha.render('cf-inline-turnstile', {
                                sitekey: '{{ $siteKey }}',
                                callback: () => this.onInlineTurnstileComplete(),
                            });
                        }
                    },

                    /**
                     * Hide inline Turnstile
                     */
                    hideInlineTurnstile() {
                        const container = document.getElementById('inline-turnstile-container');
                        if (container) {
                            container.classList.add('hidden');
                        }
                    },

                    /**
                     * Handle completed inline Turnstile
                     */
                    onInlineTurnstileComplete() {
                        console.log('✓ Inline Turnstile completed');
                        this.hideInlineTurnstile();
                        this.$dispatch('turnstile-verified');
                    }
                };
            }

            /**
             * Turnstile Callback Functions
             */

            // Called when invisible Turnstile is successful
            function onInvisibleTurnstileSuccess(token) {
                console.log('✓ Invisible Turnstile successful');
                document.getElementById('invisible-turnstile-token').value = token;
                
                // Dispatch to Alpine component
                const container = document.querySelector('[x-data="invisibleTurnstile()"]');
                if (container && container.__x) {
                    container.__x.$data.onTokenReceived(token);
                }
            }

            // Called on Turnstile error
            function onInvisibleTurnstileError(errorData) {
                console.error('✗ Invisible Turnstile error:', errorData);
                document.getElementById('invisible-turnstile-token').value = '';
            }

            // Called when token expires
            function onInvisibleTurnstileExpired() {
                console.warn('⚠ Invisible Turnstile token expired');
                document.getElementById('invisible-turnstile-token').value = '';
                // Will auto-refresh
            }

            // Called on timeout
            function onInvisibleTurnstileTimeout() {
                console.warn('⚠ Invisible Turnstile timeout - will retry');
            }

            /**
             * Livewire Event Listeners
             */
            document.addEventListener('livewire:updated', function() {
                // Handle Livewire updates if needed
            });

            // Dispatch Turnstile when needed
            document.addEventListener('dispatch-invisible-turnstile', function() {
                grecaptcha.execute();
            });
        </script>

        <!-- Load Turnstile API if not already loaded -->
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @else
        <!-- Fallback when Turnstile is not configured -->
        <div class="hidden text-xs text-slate-500 dark:text-zinc-400">
            Turnstile security widget is not configured. Please add CLOUDFLARE_TURNSTILE_SITE_KEY to .env
        </div>
    @endif
</div>

<style>
    /* Ensure invisible turnstile doesn't affect layout */
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

    /* Smooth transitions for alerts */
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

    /* Loading spinner animation */
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
