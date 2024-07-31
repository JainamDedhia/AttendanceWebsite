// Function to handle checkout process
function handleCheckout(event) {
    event.preventDefault();

    // Clear session via fetch API
    fetch('clear-session-user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Construct URL with adjusted checkout time parameter
            const checkoutUrl = `checkout-time.html?time=${encodeURIComponent(data.adjusted_checkout_time)}`;

            // Navigate to checkout-time.html with adjusted checkout time in URL
            window.location.href = checkoutUrl;
        } else {
            throw new Error('Failed to clear session');
        }
    })
    .catch(error => {
        console.error('Error clearing session:', error);
        // Handle error as needed
    });
}

// Event listener for checkout button click
document.getElementById('checkout-link').addEventListener('click', handleCheckout);
document.getElementById('admin-checkout-link').addEventListener('click', handleCheckout);
