<script>
function maskPin(input) {
    let value = input.value.replace(/\D/g, ''); // Remove any non-digit characters
    if (value.length > 4) value = value.slice(0, 4); // Ensure the max length is 4
    input.value = value.replace(/./g, '*'); // Replace each character with *
}
</script>