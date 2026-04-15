<script>
    (function () {
        const authForm = document.getElementById('auth');

        if (!authForm) {
            return;
        }

        authForm.dataset.busyForm = '1';
        authForm.dataset.busyMessage = authForm.dataset.busyMessage || 'Authenticating, please wait...';

        authForm.addEventListener('submit', function () {
            const submitButton = authForm.querySelector('button[type="submit"]');

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.setAttribute('aria-busy', 'true');
            }

            if (window.setAppBusy) {
                window.setAppBusy(true, authForm.dataset.busyMessage);
            }
        });
    })();
</script>
