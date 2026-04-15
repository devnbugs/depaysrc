// Function to show the processing modal
  function showProcessingModal() {
    document.getElementById('processingModal').style.display = 'flex';
  }

  // Function to hide the processing modal
  function hideProcessingModal() {
    document.getElementById('processingModal').style.display = 'none';
  }

  // Event listener for the Buy button click
  document.getElementById('buyButton').addEventListener('click', function () {
    // Show processing modal when the button is clicked
    showProcessingModal();

    // Simulate an asynchronous request (replace this with your actual request)
    setTimeout(function () {
      // Hide processing modal when the response is returned (replace this with your actual response handling)
      hideProcessingModal();
    }, 2000); // Simulating a 2-second delay, replace with your actual request time
  });