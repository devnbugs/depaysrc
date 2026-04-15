@script
<script>
    Livewire.on('passkeyPropertiesValidated', async function (eventData) {
        const passkeyOptions = eventData[0].passkeyOptions;

        try {
            const passkey = await startRegistration({ optionsJSON: passkeyOptions });
            @this.call('storePasskey', JSON.stringify(passkey));
        } catch (_) {
            if (typeof window.depayAlert === 'function') {
                window.depayAlert({
                    type: 'error',
                    title: 'Passkey setup',
                    message: 'Something went wrong. Please try again.',
                });
                return;
            }

            window.alert('Something went wrong. Please try again.');
        }
    });
</script>
@endscript
