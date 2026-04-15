<script>
    window.authenticateWithPasskey = async function (remember = false) {
        const showFailure = () => {
            if (typeof window.depayAlert === 'function') {
                window.depayAlert({
                    type: 'error',
                    title: 'Passkey sign-in',
                    message: 'Something went wrong. Please try again.',
                });
                return;
            }

            window.alert('Something went wrong. Please try again.');
        };

        try {
            const response = await fetch('{{ route('passkeys.authentication_options') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Unable to initialize passkey authentication.');
            }

            const options = await response.json();
            const startAuthenticationResponse = await startAuthentication({ optionsJSON: options });
            const form = document.getElementById('passkey-login-form');

            if (!form) {
                throw new Error('Passkey form is unavailable.');
            }

            const rememberInput = form.querySelector('input[name="remember"]') || document.createElement('input');
            rememberInput.type = 'hidden';
            rememberInput.name = 'remember';
            rememberInput.value = remember ? '1' : '0';

            if (!rememberInput.parentNode) {
                form.appendChild(rememberInput);
            }

            const responseInput = form.querySelector('input[name="start_authentication_response"]') || document.createElement('input');
            responseInput.type = 'hidden';
            responseInput.name = 'start_authentication_response';
            responseInput.value = JSON.stringify(startAuthenticationResponse);

            if (!responseInput.parentNode) {
                form.appendChild(responseInput);
            }

            form.submit();
        } catch (_) {
            showFailure();
        }
    };
</script>
