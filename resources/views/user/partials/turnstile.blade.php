@if(config('services.cloudflare.turnstile_site_key'))
    <div class="space-y-2">
        <div class="cf-turnstile" data-sitekey="{{ config('services.cloudflare.turnstile_site_key') }}" data-theme="light"></div>
        @error('cf-turnstile-response')
            <p class="text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    @if($loop->first ?? true)
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
@endif
