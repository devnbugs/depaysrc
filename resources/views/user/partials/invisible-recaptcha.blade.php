@if(config('services.cloudflare.recaptcha_site_key'))
    <input type="hidden" name="cf-recaptcha-token" id="cf-recaptcha-token">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script>
        (function() {
            'use strict';
            const form = document.getElementById('{{ $formId ?? "purchase" }}');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const siteKey = '{{ config("services.cloudflare.recaptcha_site_key") }}';
                
                // Create an invisible Turnstile widget (configured as invisible in Cloudflare)
                turnstile.render('#cf-recaptcha-token', {
                    sitekey: siteKey,
                    theme: 'light',
                    callback: function(token) {
                        // Token obtained, submit the form
                        document.getElementById('{{ $formId ?? "purchase" }}').submit();
                    }
                });
            });
        })();
    </script>
@endif
