<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Dashboard, Leave Application, Leave Application Dashboard, and Checkout Option</title>
    <link rel="stylesheet" href="adlacuser.css">
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

h2 {
    text-align: center;
    margin-top: 100px;
    font-size: 50px;
    color: white;
    
  }
 
  h3 {
    margin-top: 2px;
    font-size: 22px;
    margin-bottom: 1px;
    color: white;
  }
  #geofenceStatus {
    color: white;
}
</style>
<body>
    <h2>Welcome!</h2>
    
    <marquee behavior="alternate" direction="left"><h3>Mark your attendance, view your attendance and leave request/s, fill leave application or simply checkout!</h3></marquee> 

    <div class="btn1">
    <button class="adlac" onclick="prepareAndRecordAttendance()">Mark attendance</button>
    </div>
    <div class="btn2">
        <a href="dashboard.php" target="_blank"><button class="adlac">Attendance Dashboard</button></a>
    </div>

    <div class="btn3">
        <a href="leaveappform.php" target="_blank"><button class="adlac">Leave Application</button></a>
    </div>

    <div class="btn4">
        <a href="view_leave.php" target="_blank"><button class="adlac">Leave Application Dashboard</button></a>
    </div>

    <div class="btn5">
        <a id="checkout-link" href="checkout-time.html"><button class="adlac">Checkout</button></a>
    </div>
    <form id="attendanceForm">
            <!-- Hidden fields for ID, Date of Birth, Status, Latitude, and Longitude -->
            <input type="hidden" id="id" name="id">
            <input type="hidden" id="dob" name="dob">
            <input type="hidden" id="status" name="status">
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">

            <div class="form-group">
                <input type="button" value="Submit" onclick="prepareAndRecordAttendance()">
            </div>
        </form>
        <div id="message"></div>
        <div id="geofenceStatus" style="margin-top: 20px; font-weight: bold;">
    
        </div>
        
        <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if the user is not logged in
    if (!isset($_SESSION['username'])) {
        // Redirect unauthorized users to the login page
        header("Location: login.php");
        exit(); // Stop further execution
    }
    
    // Check if employeeId is set in the session
    if (isset($_SESSION['employeeId'])) {
        $employeeId = $_SESSION['employeeId'];
        echo "<script>var employeeId = '$employeeId'; console.log('Employee ID:', employeeId);</script>";
    } else {
        echo "<script>alert('Employee ID not found in session.');</script>";
        exit(); // Stop further execution if employeeId is not set
    }
    ?>
    <script>
    function formatTime(date) {
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        const seconds = date.getSeconds().toString().padStart(2, '0');
        return `${hours}:${minutes}:${seconds}`;
    }

    // Event listener to set checkout time in URL on click
    document.getElementById('checkout-link').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default action of anchor tag

        // Clear session via fetch API
        fetch('clear-session-user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ logout: true })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to clear session');
            }
            return response.json();
        })
        .then(data => {
            console.log('Response from clear-session-user.php:', data); // Log the response

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
    var checkingInterval; // Variable to hold the interval ID

        function prepareAndRecordAttendance() {
            // Set the hidden fields with session and predefined values
            document.getElementById('id').value = employeeId;
            document.getElementById('dob').value = new Date().toISOString().split('T')[0];
            document.getElementById('status').value = "Present";

            console.log('Preparing to record attendance with ID:', employeeId, 'Date:', new Date().toISOString().split('T')[0]);

            recordAttendance();
        }

        function recordAttendance() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;
                    
                    document.getElementById('latitude').value = latitude;
                    document.getElementById('longitude').value = longitude;

                    var formData = new FormData(document.getElementById('attendanceForm'));

                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "record_attendance.php", true);
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4) {
                            if (xhr.status == 200) {
                                var response = JSON.parse(xhr.responseText);
                                alert(response.message);
                                if (response.message.includes("Attendance recorded successfully")) {
                                    // Start periodic location check
                                    startLocationCheck();
                                }
                            } else {
                                console.error('Error: ' + xhr.status);
                            }
                        }
                    };
                    xhr.send(formData);

                }, function(error) {
                    document.getElementById('geofenceStatus').innerText = 'Error getting location: ' + error.message;
                });
            } else {
                document.getElementById('geofenceStatus').innerText = 'Geolocation is not supported by this browser.';
            }
        }
        function startLocationCheck() {
            // Clear any existing interval to avoid duplicates
            clearInterval(checkingInterval);

            // Start checking location every 10 seconds
            checkingInterval = setInterval(function() {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "check_location.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var response = JSON.parse(xhr.responseText);
                        console.log(response.message); // Log the message received
                        document.getElementById('geofenceStatus').innerText = response.message;
                    }
                };
                xhr.send("latitude=" + document.getElementById('latitude').value +
                         "&longitude=" + document.getElementById('longitude').value +
                         "&id=" + document.getElementById('id').value +
                         "&dob=" + document.getElementById('dob').value);
            }, 10000); // 10 seconds interval
        }

        // Optional: Set today's date on window load
        window.onload = function() {
            var today = new Date().toISOString().split('T')[0];
            document.getElementById('dob').value = today;
        };
    </script>
</body>
</html>
