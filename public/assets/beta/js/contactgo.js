<script>
    document.getElementById('selectContact').addEventListener('click', async () => {
        if (!navigator.contacts || !navigator.contacts.select) {
            alert('Your browser does not support contact selection.');
            return;
        }

        try {
            const contacts = await navigator.contacts.select(['tel'], { multiple: false });

            if (contacts.length > 0) {
                let phoneNumber = contacts[0].tel[0];
                
                // Remove the +234 prefix if it exists
                phoneNumber = phoneNumber.startsWith('+234') 
                    ? phoneNumber.replace('+234', '0') 
                    : phoneNumber;

                // Update the input field
                document.getElementById('phone').value = phoneNumber;
            }
        } catch (error) {
            console.error('Contact selection failed:', error);
            alert('Failed to select a contact.');
        }
    });
</script>
