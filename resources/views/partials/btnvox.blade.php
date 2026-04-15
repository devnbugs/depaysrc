<script>
    (function () {
        const purchaseForm = document.getElementById('purchase');

        if (!purchaseForm) {
            return;
        }

        purchaseForm.dataset.busyForm = '1';
        purchaseForm.dataset.busyMessage = purchaseForm.dataset.busyMessage || 'Processing payment, please wait...';
        purchaseForm.dataset.confirmForm = '1';
        purchaseForm.dataset.confirmSummary = purchaseForm.dataset.confirmSummary || 'auto';
        purchaseForm.dataset.confirmTitle = purchaseForm.dataset.confirmTitle || 'Confirm purchase';
        purchaseForm.dataset.confirmMessage = purchaseForm.dataset.confirmMessage || 'Please review the phone number, amount, and package before you continue.';
        purchaseForm.dataset.confirmTone = purchaseForm.dataset.confirmTone || 'warning';
        purchaseForm.dataset.confirmAcceptText = purchaseForm.dataset.confirmAcceptText || 'Proceed';
    })();
</script>
