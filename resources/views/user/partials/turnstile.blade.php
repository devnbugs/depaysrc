    <div class="space-y-2">
        <!-- Cloudflare Turnstile: Managed Interactive Widget -->
        <!-- Features: Auto Theme, Flexible Size (Responsive), Auto Refresh, Auto Retry, Always Visible -->
        <div id="cf-turnstile-{{ uniqid('turnstile_') }}" 
             class="cf-turnstile" 
             data-sitekey="{{ config('services.cloudflare.turnstile_site_key') }}"
             data-theme="auto"
             data-size="flexible"
             data-appearance="always"
             data-execution="render"
             data-language="auto"
             data-tabindex="0"
             data-callback="onTurnstileSuccess"
             data-error-callback="onTurnstileError"
             data-expired-callback="onTurnstileExpired"
             data-timeout-callback="onTurnstileTimeout"
             data-before-interactive-callback="onBeforeTurnstileInteractive"
             data-after-interactive-callback="onAfterTurnstileInteractive"
             data-retry="auto"
             data-retry-interval="8000"
             data-refresh-expired="auto"
             data-refresh-timeout="auto"
             data-response-field="true"
             data-response-field-name="cf-turnstile-response"
             data-feedback-enabled="true"
        ></div>

        <!-- Hidden input to store token -->
        <input type="hidden" name="cf-turnstile-response" id="cf-turnstile-response" value="">

        @error('cf-turnstile-response')
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 dark:border-red-900/20 dark:bg-red-900/10">
                <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ $message }}</p>
            </div>
        @enderror
    </div>

    @if($loop->first ?? true)
        <!-- Cloudflare Turnstile API Script -->
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

        <script>
            /**
             * Turnstile Widget Callbacks
             * Manages responsive design and auto-execution features
             */
            
            // Called when user completes challenge
            function onTurnstileSuccess(token) {
                console.log('✓ Turnstile verification successful');
                document.getElementById('cf-turnstile-response').value = token;
                
                // Remove error class if exists
                const errorElement = document.querySelector('[data-turnstile-error]');
                if (errorElement) {
                    errorElement.classList.remove('!ring-2', '!ring-red-500', '!ring-opacity-50');
                }
                
                // Trigger form submission if auto-submit is enabled
@if(config('services.cloudflare.turnstile_site_key'))
    <div class="space-y-2">
        <!-- Cloudflare Turnstile: Managed Interactive Widget -->
        <!-- Enhanced for cf_clearance and Cloudflare protection -->
        <div id="cf-turnstile-{{ uniqid('turnstile_') }}"
             class="cf-turnstile"
             data-sitekey="{{ config('services.cloudflare.turnstile_site_key') }}"
             data-theme="auto"
             data-size="flexible"
             data-appearance="always"
             data-execution="render"
             data-language="auto"
             data-tabindex="0"
             data-callback="onTurnstileSuccess"
             data-error-callback="onTurnstileError"
             data-expired-callback="onTurnstileExpired"
             data-timeout-callback="onTurnstileTimeout"
             data-before-interactive-callback="onBeforeTurnstileInteractive"
             data-after-interactive-callback="onAfterTurnstileInteractive"
             data-retry="auto"
             data-retry-interval="8000"
             data-refresh-expired="auto"
             data-refresh-timeout="auto"
             data-response-field="true"
             data-response-field-name="cf-turnstile-response"
             data-feedback-enabled="true"
        ></div>

        <!-- Hidden input to store token -->
        <input type="hidden" name="cf-turnstile-response" id="cf-turnstile-response" value="">

        @error('cf-turnstile-response')
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 dark:border-red-900/20 dark:bg-red-900/10">
                <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ $message }}</p>
            </div>
        @enderror
    </div>

    @if($loop->first ?? true)
        <!-- Cloudflare Turnstile API Script -->
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

        <script>
            // Enhanced Turnstile Widget Callbacks for cf_clearance
            function onTurnstileSuccess(token) {
                console.log('\u2713 Turnstile verification successful');
                document.getElementById('cf-turnstile-response').value = token;
                // Remove error class if exists
                const errorElement = document.querySelector('[data-turnstile-error]');
                if (errorElement) {
                    errorElement.classList.remove('!ring-2', '!ring-red-500', '!ring-opacity-50');
                }
            }
            function onTurnstileError() {
                console.warn('Turnstile error. If you are using Cloudflare cf_clearance, ensure cookies are present.');
            }
            function onTurnstileExpired() {
                document.getElementById('cf-turnstile-response').value = '';
            }
            function onTurnstileTimeout() {
                document.getElementById('cf-turnstile-response').value = '';
            }
            function onBeforeTurnstileInteractive() {}
            function onAfterTurnstileInteractive() {}
        </script>
    @endif
@endif
                const form = document.querySelector('form[data-turnstile-auto-submit]');
                if (form && form.dataset.turnstileAutoSubmit === 'true') {
                    form.submit();
                }
            }

            // Called when user closes challenge
            function onTurnstileError(errorData) {
                console.error('✗ Turnstile error:', errorData);
                document.getElementById('cf-turnstile-response').value = '';
                
                // Add error styling
                const widget = document.querySelector('.cf-turnstile');
                if (widget) {
                    widget.classList.add('!ring-2', '!ring-red-500', '!ring-opacity-50');
                }
            }

            // Called when challenge expires (14400000ms = 4 hours)
            function onTurnstileExpired() {
                console.warn('⚠ Turnstile token expired - will auto-refresh');
                document.getElementById('cf-turnstile-response').value = '';
                // Auto-refresh is handled by data-auto-reset-timeout
            }

            // Called on timeout
            function onTurnstileTimeout() {
                console.warn('⚠ Turnstile timeout - will auto-retry');
                // Auto-retry is handled by data-retry="auto"
            }

            // Called before widget becomes interactive
            function onBeforeTurnstileInteractive() {
                console.log('↻ Turnstile widget loading...');
                // Disable submit button during loading
                const submitBtn = document.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }

            // Called after widget becomes interactive
            function onAfterTurnstileInteractive() {
                console.log('→ Turnstile widget ready for interaction');
                // Enable submit button
                const submitBtn = document.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            /**
             * Responsive Design Handler
             * Auto-adjust widget size based on container and button width
             */
            function setupResponsiveDesign() {
                const widget = document.querySelector('.cf-turnstile');
                const form = document.querySelector('form');
                const submitBtn = document.querySelector('button[type="submit"]');

                if (!widget || !form || !submitBtn) return;

                function adjustWidgetSize() {
                    const formWidth = form.offsetWidth;
                    const submitBtnWidth = submitBtn.offsetWidth;
                    
                    // Size the widget to match form width while respecting button width
                    const maxWidth = Math.min(formWidth, 300);
                    const minWidth = Math.max(submitBtnWidth, 150);
                    
                    widget.style.width = 'auto';
                    widget.style.maxWidth = formWidth + 'px';

                    // Adjust widget styling
                    if (formWidth < 300) {
                        widget.style.transform = 'scale(0.9)';
                        widget.style.transformOrigin = 'left center';
                    } else {
                        widget.style.transform = 'scale(1)';
                    }
                }

                // Initial adjustment
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', adjustWidgetSize);
                } else {
                    adjustWidgetSize();
                }

                // Adjust on window resize
                window.addEventListener('resize', adjustWidgetSize, { passive: true });
                
                // Adjust when Turnstile is ready
                const observer = new MutationObserver(adjustWidgetSize);
                observer.observe(widget, { 
                    childList: true, 
                    subtree: true, 
                    attributes: true 
                });
            }

            // Initialize on page load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setupResponsiveDesign);
            } else {
                setupResponsiveDesign();
            }

            /**
             * Form Validation Handler
             * Ensures Turnstile is completed before submission
             */
            document.addEventListener('DOMContentLoaded', function() {
                const forms = document.querySelectorAll('form');
                
                forms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        const token = document.getElementById('cf-turnstile-response');
                        
                        if (token && !token.value) {
                            console.error('Turnstile verification required');
                            e.preventDefault();
                            e.stopPropagation();
                            
                            // Focus on turnstile widget
                            const widget = document.querySelector('.cf-turnstile');
                            if (widget) {
                                widget.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            
                            return false;
                        }
                    });
                });
            });
        </script>
    @endif
@endif
