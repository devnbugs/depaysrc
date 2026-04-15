    // Get references to the elements
    var copyIcon = document.getElementById("copyIcon");
    var copiedMessage = document.getElementById("copiedMessage");
    var copyText = document.getElementById("copyText");
    var popupLink = document.getElementById("popupLink");

    // Set the text to copy
    var textToCopy = "{{ $user->aNo1 }} - {{ $user->aN1 }}";

    // Add a click event listener to the copy icon
    copyIcon.addEventListener("click", function() {
        // Copy the text to the clipboard
        copyText.value = textToCopy;
        copyText.select();
        document.execCommand("copy");
        
        // Show the "Copied!" message briefly
        copiedMessage.style.display = "inline";
        setTimeout(function() {
            copiedMessage.style.display = "none";
        }, 2000); // Hide the message after 2 seconds
    });

    // Optionally, you can also open the popup when clicking the link
    popupLink.addEventListener("click", function() {
        // Open the popup here if needed
    });