<div>
    @include('passkeys::components.partials.authenticateScript')

    <form id="passkey-login-form" method="POST" action="{{ route('passkeys.login') }}">
        @csrf
    </form>

    @if (session()->has('authenticatePasskey::message'))
        <script>
            window.__depayFlashQueue = window.__depayFlashQueue || [];
            window.__depayFlashQueue.push({
                kind: 'alertx',
                type: 'error',
                title: 'Passkey sign-in',
                message: 'Something went wrong. Please try again.',
            });
        </script>
    @endif

    <div onclick="window.authenticateWithPasskey && window.authenticateWithPasskey()">
        @if ($slot->isEmpty())
            <div class="underline cursor-pointer">
                {{ __('passkeys::passkeys.authenticate_using_passkey') }}
            </div>
        @else
            {{ $slot }}
        @endif
    </div>
</div>
