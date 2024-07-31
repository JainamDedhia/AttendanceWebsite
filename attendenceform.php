<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Form</title>
    <link rel="stylesheet" href="attstyles.css">
</head>
<body>
    <div class="container">
        <img src="attend.jpeg" alt="Attendance Image">
        <h2>Attendance Form</h2>
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
        <div id="geofenceStatus" style="margin-top: 20px; font-weight: bold;"></div>
    </div>

    <!-- Your PHP session and script -->
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

    <!-- JavaScript for attendance form functionality -->
    <script>
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
