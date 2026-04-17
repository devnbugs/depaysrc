
    @if(config('services.cloudflare.turnstile_site_key'))
    <div class="space-y-2">
        <!-- Cloudflare Turnstile: Managed Interactive Widget -->
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

    <!-- Cloudflare Turnstile API Script -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script>
        // Turnstile Widget Callbacks and Responsive Design
        function onTurnstileSuccess(token) {
            document.getElementById('cf-turnstile-response').value = token;
            const errorElement = document.querySelector('[data-turnstile-error]');
            if (errorElement) {
                errorElement.classList.remove('!ring-2', '!ring-red-500', '!ring-opacity-50');
            }
        }
        function onTurnstileError() {
            document.getElementById('cf-turnstile-response').value = '';
            const widget = document.querySelector('.cf-turnstile');
            if (widget) {
                widget.classList.add('!ring-2', '!ring-red-500', '!ring-opacity-50');
            }
        }
        function onTurnstileExpired() {
            document.getElementById('cf-turnstile-response').value = '';
        }
        function onTurnstileTimeout() {
            document.getElementById('cf-turnstile-response').value = '';
        }
        function onBeforeTurnstileInteractive() {
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
        function onAfterTurnstileInteractive() {
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
        // Responsive Design Handler
        function setupResponsiveDesign() {
            const widget = document.querySelector('.cf-turnstile');
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('button[type="submit"]');
            if (!widget || !form || !submitBtn) return;
            function adjustWidgetSize() {
                const formWidth = form.offsetWidth;
                widget.style.maxWidth = formWidth + 'px';
                if (formWidth < 300) {
                    widget.style.transform = 'scale(0.9)';
                    widget.style.transformOrigin = 'left center';
                } else {
                    widget.style.transform = 'scale(1)';
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', adjustWidgetSize);
            } else {
                adjustWidgetSize();
            }
            window.addEventListener('resize', adjustWidgetSize, { passive: true });
            const observer = new MutationObserver(adjustWidgetSize);
            observer.observe(widget, { childList: true, subtree: true, attributes: true });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupResponsiveDesign);
        } else {
            setupResponsiveDesign();
        }
        // Form Validation Handler
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const token = document.getElementById('cf-turnstile-response');
                    if (token && !token.value) {
                        e.preventDefault();
                        e.stopPropagation();
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