<script>
    document.getElementById('proceed').addEventListener('click', function() {
        Swal.fire({
            title: 'Hello!',
            text: 'This is a SweetAlert triggered by a button click.',
            icon: 'success', // Type of alert: success, error, warning, info, question
            confirmButtonText: 'Okay'
        });
    });
</script>