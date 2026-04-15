var popupVisible = false;
var popupStartTime;

function setup() {
    var canvas = createCanvas(windowWidth, windowHeight);
    canvas.parent('popupCanvas');
    noLoop();
}

function draw() {
    clear();
    if (popupVisible) {
        // Draw a semi-transparent overlay
        fill(0, 0, 0, 150);
        rect(0, 0, width, height);

        // Draw the popup box
        fill(255);
        var popupWidth = 300;
        var popupHeight = 150;
        var popupX = width / 2 - popupWidth / 2;
        var popupY = height / 2 - popupHeight / 2;
        rect(popupX, popupY, popupWidth, popupHeight, 10);

        // Draw the popup content
        fill(0);
        textSize(20);
        textAlign(CENTER, CENTER);
        text("Processing Popup", width / 2, height / 2 - 20);

        // Simulate an asynchronous process (e.g., making an API request)
        var currentTime = millis();
        if (currentTime - popupStartTime > 2000) {
            // After the response is returned, hide the popup
            hidePopup();
        }
    }
}

// Function to show a customized SweetAlert2-like popup without the OK button
function showPopup() {
    popupVisible = true;

    // Disable the draw loop
    noLoop();

    // Set the start time for the popup
    popupStartTime = millis();
}

// Function to hide the SweetAlert2-like popup
function hidePopup() {
    popupVisible = false;

    // Enable the draw loop
    loop();
}

// Handle mouse clicks
function mousePressed() {
    // Check if the popup is visible and the click is outside the popup
    if (popupVisible && mouseX < width / 2 - 150 || mouseX > width / 2 + 150 || mouseY < height / 2 - 75 || mouseY > height / 2 + 75) {
        // Hide the popup when clicking outside
        hidePopup();
    }
}

// Function to handle window resizing
function windowResized() {
    resizeCanvas(windowWidth, windowHeight);
}
