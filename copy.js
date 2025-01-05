function copyCouponCode() {
    // Get the coupon code text
    const couponCode = document.getElementById("coupon-code").textContent;

    // Create a temporary text area to copy the text
    const textArea = document.createElement("textarea");
    textArea.value = couponCode;
    document.body.appendChild(textArea);

    // Select and copy the text
    textArea.select();
    document.execCommand("copy");

    // Remove the temporary text area
    document.body.removeChild(textArea);

    // Show the notification with "Copied!" message
    const notification = document.getElementById("copy-notification");
    notification.style.display = "block";  // Make the notification visible

    // Add the fade-out effect after 1 second
    setTimeout(() => {
        notification.classList.add("fade-out");
    }, 1000);

    // Hide the notification completely after it fades out
    setTimeout(() => {
        notification.style.display = "none";
        notification.classList.remove("fade-out");
    }, 2000);
}
