<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance dashboard, Leave Application and checkout option</title>
    
</head>
<style>
    body {
  font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  background-image: url('adlac4.jpeg');
  background-size: cover; /* Ensures the background image covers the entire body */
  background-position: center; /* Centers the background image */
  background-repeat: no-repeat; /* Prevents the background image from repeating */
  min-height: 100vh; /* Ensures the body takes at least the full height of the viewport */
}


h2{
    text-align: center;
    margin-top: 100px;
    font-size: 50px;
    font-family: ;
    color: white;
}

h3{
  margin-top: 2px;
  font-size: 28px;
  margin-bottom: 1px;
  text-align: center;
  color: white;
}

.adlac,
.adlac *,
.adlac :after,
.adlac :before,
.adlac:after,
.adlac:before {
  border: 3px solid black;
  box-sizing: border-box;
}

.adlac {
  -webkit-tap-highlight-color: 
transparent;
  -webkit-appearance: button;
  background-color: #fff;
  background-image: none;
  color: #000;
  cursor: pointer;
  font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont,
    Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif,
    Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
  font-size: 22px;
  line-height: 1.5;
  margin: 0;
  -webkit-mask-image: -webkit-radial-gradient(#000, #fff);
  padding: 0;
}

.adlac:disabled {
  cursor: default;
}

.adlac:-moz-focusring {
  outline: auto;
}

.adlac svg {
  display: block;
  vertical-align: middle;
}

.adlac [hidden] {
  display: none;
}

.adlac {
  border: 3px solid;
  border-radius: 100px;
  box-sizing: border-box;
  display: inline-block;
  font-weight: 900;
  -webkit-mask-image: none;
  overflow: hidden;
  padding: 1rem 3rem;
  position: relative;
  text-transform: uppercase;
  margin: 9px 0;
}

.adlac:hover {
  -webkit-animation: pulse 0.5s;
  animation: pulse 0.5s;
  box-shadow: 0 0 0 2em 
transparent;
}

.btn1{
    width: 200px;
    text-align: center;
    margin-top: 36px; /* add some space from the top */
    position: relative;
    top: 3px;
    bottom: 10px;
}

.btn2{
  width: 200px;
  text-align: center;
  margin-top: 10px; /* add some space from the top */
  position: relative;
  top: 15px;
  bottom: 10px;
  right: 5px;
}

.btn3{
  width: 200px;
  text-align: center;
  margin-top: 10px; /* add some space from the top */
  position: relative;
  top: 25px;
  bottom: 10px;
  left: 15px;
}

@-webkit-keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 
#000;
  }
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 
#000;
  }
}

a:link{
    color: #fff;
    text-decoration: none;
}

a:visited{
    color: #fff;
    text-decoration: none;
}  

</style>
<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    // Redirect unauthorized users to the login page or another appropriate page
    header("Location: login.php");
    exit();
}
?>

<body>
    <h2>Welcome <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    
    <h3>View the employees' attendance, accept/reject leave applications or simply checkout!</h3>

<div class="btn1">
    <a href="dashboard.php" target="_blank"><button class="adlac">Attendance Monitor</button></a>
</div>

<div class="btn2">
    <a href="dashboard2.php" target="_blank"><button class="adlac">Leave Applications Viewer</button></a>
</div>

<div class="btn3">
    <a id="admin-checkout-link" href="checkout-time.html" target="_blank"><button class="adlac">Checkout</button></a>
</div>

<script>
    function formatTime(date) {
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const seconds = date.getSeconds().toString().padStart(2, '0');
    return `${hours}:${minutes}:${seconds}`;
}

// Event listener to set checkout time in URL on click
document.getElementById('admin-checkout-link').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default action of anchor tag

    // Clear session via fetch API
    fetch('clear-session-admin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ logout: true })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Get current time
            const now = new Date();
            const checkoutTime = formatTime(now);

            // Construct URL with checkout time parameter
            const checkoutUrl = `checkout-time.html?time=${encodeURIComponent(checkoutTime)}`;

            // Navigate to checkout-time.html with checkout time in URL
            window.location.href = checkoutUrl;
        } else {
            throw new Error('Failed to clear session');
        }
    })
    .catch(error => {
        console.error('Error clearing session:', error);
        // Handle error as needed
    });
});
</script>

</body>
</html>