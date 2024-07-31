<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance and Leave Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 600px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .buttons {
            text-align: center;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            margin: 5px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        button:hover {
            transform: scale(1.05);
        }
        button:active {
            transform: scale(0.95);
        }
        button[name="mark_attendance"],
        button[name="view_attendance"],
        button[name="apply_leave"],
        button[name="view_leaves"],
        button[name="checkout"] {
            background-color: #ff9800;
            color: white;
        }
        button[name="mark_attendance"]:hover,
        button[name="view_attendance"]:hover,
        button[name="apply_leave"]:hover,
        button[name="view_leaves"]:hover,
        button[name="checkout"]:hover {
            background-color: #fb8c00;
        }
        .content {
            margin-top: 20px;
        }
        form label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        form input, form textarea {
            width: calc(100% - 22px);
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        form button:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        // Predefined office location (latitude and longitude)
        const officeLatitude = 19.190275; // Replace with your office latitude
        const officeLongitude = 72.950783; // Replace with your office longitude
        const allowedRadius = 100; // Allowed radius in meters

        // Calculate distance between two points using Haversine formula
        function getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Radius of the Earth in meters
            const dLat = (lat2 - lat1) * (Math.PI / 180);
            const dLon = (lon2 - lon1) * (Math.PI / 180);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const distance = R * c; // Distance in meters
            return distance;
        }

        // Check user's location
        function checkLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const userLatitude = position.coords.latitude;
                    const userLongitude = position.coords.longitude;

                    const distance = getDistanceFromLatLonInMeters(
                        officeLatitude,
                        officeLongitude,
                        userLatitude,
                        userLongitude
                    );

                    if (distance <= allowedRadius) {
                        document.getElementById('attendanceForm').submit();
                    } else {
                        alert('You are not within the 100-meter radius of the office. Attendance not marked.');
                    }
                }, error => {
                    alert('Geolocation error: ' + error.message);
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Attendance and Leave Management</h1>

        <div class="buttons">
            <form method="POST" action="" id="attendanceForm">
                <button type="button" name="mark_attendance" onclick="checkLocation()">Mark Attendance</button>
                <button type="submit" name="view_attendance">Attendance Dashboard</button>
                <button type="submit" name="apply_leave">Leave Application</button>
                <button type="submit" name="view_leaves">Leave Dashboard</button>
                <button type="submit" name="checkout">Checkout</button>
            </form>
        </div>

        <div class="content">
            <?php
        

            // Initialize attendance and leave data if not already set
            if (!isset($_SESSION['attendance'])) {
                $_SESSION['attendance'] = [];
            }
            if (!isset($_SESSION['leaves'])) {
                $_SESSION['leaves'] = [];
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['mark_attendance'])) {
                    markAttendance();
                } elseif (isset($_POST['view_attendance'])) {
                    viewAttendance();
                } elseif (isset($_POST['apply_leave'])) {
                    leaveApplicationForm();
                } elseif (isset($_POST['submit_leave'])) {
                    submitLeave();
                } elseif (isset($_POST['view_leaves'])) {
                    viewLeaves();
                } elseif (isset($_POST['checkout'])) {
                    checkout();
                }
            }

            function markAttendance() {
                $_SESSION['attendance'][] = date("Y-m-d H:i:s");
                echo "<h2>Attendance Marked</h2>";
                echo "Attendance recorded at: " . end($_SESSION['attendance']);
            }

            function viewAttendance() {
                echo "<h2>Attendance Dashboard</h2>";
                if (empty($_SESSION['attendance'])) {
                    echo "No attendance records.";
                } else {
                    echo "<ul>";
                    foreach ($_SESSION['attendance'] as $record) {
                        echo "<li>$record</li>";
                    }
                    echo "</ul>";
                }
            }

            function leaveApplicationForm() {
                echo "
                    <h2>Leave Application</h2>
                    <form method='POST' action=''>
                        <label for='leave_date'>Leave Date:</label>
                        <input type='date' id='leave_date' name='leave_date' required>
                        <label for='reason'>Reason:</label>
                        <textarea id='reason' name='reason' required></textarea>
                        <button type='submit' name='submit_leave'>Submit Leave</button>
                    </form>
                ";
            }

            function submitLeave() {
                $leave = [
                    'date' => $_POST['leave_date'],
                    'reason' => $_POST['reason']
                ];
                $_SESSION['leaves'][] = $leave;
                echo "<h2>Leave Submitted</h2>";
                echo "Leave Date: " . $leave['date'] . "<br>";
                echo "Reason: " . $leave['reason'];
            }

            function viewLeaves() {
                echo "<h2>Leave Dashboard</h2>";
                if (empty($_SESSION['leaves'])) {
                    echo "No leave applications.";
                } else {
                    echo "<ul>";
                    foreach ($_SESSION['leaves'] as $leave) {
                        echo "<li>Date: {$leave['date']}, Reason: {$leave['reason']}</li>";
                    }
                    echo "</ul>";
                }
            }

            function checkout() {
                echo "<h2>Checked Out</h2>";
                echo "You have successfully checked out.";
            }
            ?>
        </div>
    </div>
</body>
</html>
