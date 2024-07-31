<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Check if the user is logged in and is an admin


require 'vendor/autoload.php'; // Include Composer's autoload file

use MongoDB\Client as MongoClient;

// MongoDB connection string and options
$mongoURI = 'mongodb://localhost:27017';
$options = [];



// Check if the user is not logged in
if (!isset($_SESSION['username'])) {
    // Redirect unauthorized users to the login page
    header("Location: login.php");
    exit(); // Stop further execution
}



// Create a MongoDB client
try {
    $mongoClient = new MongoClient($mongoURI, $options);
    $db = $mongoClient->attendancetrial; // Select your database
    $collection = $db->attendance; // Select your collection
} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
    exit;
}

// Process form submission
$attendanceRecords = [];
$filter = []; // Initialize an empty filter

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if date is set
    if (isset($_POST['date'])) {
        $selectedDate = $_POST['date'];
        $filter['date_time'] = $selectedDate; // Match against the 'date_time' field
    }

    // Check if status is set
    if (isset($_POST['status']) && $_POST['status'] != 'all') {
        $filter['status'] = $_POST['status'];
    }

    // Query MongoDB for attendance records based on the filter
    $cursor = $collection->find($filter);

    // Track unique employee_ids to prevent duplicates
    $uniqueEmployeeIds = [];

    foreach ($cursor as $document) {
        $employeeId = $document['employee_id'];
        if (!in_array($employeeId, $uniqueEmployeeIds)) {
            $uniqueEmployeeIds[] = $employeeId;
            $attendanceRecords[] = $document;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Dashboard</title>
    <style>
        body {
            font-family: cursive;
            background-color: #f0f0f0;
            padding: 20px;
            text-align: center;
            background-image: url('dashboard2.jpg');
            background-size: cover; /* Ensures the background image covers the entire body */
            background-position: center; /* Centers the background image */
            background-repeat: repeat; /* Prevents the background image from repeating */
            min-height: 100vh; /* Ensures the body takes at least the full height of the viewport */
        }

        h2 {
            color: #7b2cbf;
            font-size: 40px;
            margin-bottom: 40px;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto; /* Center the container */
            padding-bottom: 10px;
        }

        .container img {
            width: 120px;
            height: auto;
            margin-bottom: 1px;
            display: block;
            padding-bottom: 10px;
            margin-top: 10px;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            margin-right: 10px;
        }

        input, select {
            padding: 8px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            padding: 8px 20px;
            background-color: #7b2cbf;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: rgb(75, 69, 69);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 2px solid #7b2cbf;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .centered_header {
            text-align: center;
        }

        .indicators {
            display: flex;
            justify-content: space-around;
            align-items: center;
            width: 100%;
            margin-top:30px;
            max-width: 600px;
            margin-bottom: 20px;
        }

        .indicator {
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            text-align: center;
            padding: 10px 20px;
            color: #fff;
            width: 120px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .indicator.weekend {
            background-color: #ff7b54; /* Orange-red for weekends */
        }

        .indicator.weekday {
            background-color: #e0e0e0;
            color: #333;
        }

        .indicator.holiday {
            background-color: #f3c940; /* Golden yellow for holidays */
        }

        .indicator:hover {
            transform: translateY(-2px);
        }

        .calendar-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            text-align: center;
            margin-bottom: 30px;
        }

        #yearly-calendar {
            width: 100%;
            overflow: hidden;
            border-radius: 8px;
        }

        .calendar-scroll {
            display: flex;
            align-items: center;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            margin-top: 10px;
        }

        .calendar-content {
            display: flex;
            flex-wrap: nowrap;
            scroll-snap-align: center;
            padding: 10px 0;
        }

        .scroll-left, .scroll-right {
            padding: 15px;
            font-size: 28px;
            background-color: #4a90e2;
            color: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 15px;
            transition: background-color 0.3s ease;
        }

        .scroll-left:hover, .scroll-right:hover {
            background-color: #357bd8;
        }

        /* Month container styles */
        .month-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            margin: 0 10px;
        }

        .month-header {
            background-color: #6c5ce7; /* Purple for month headers */
            color: #fff;
            padding: 10px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .month-name {
            font-size: 18px;
            margin: 0;
        }

        .month-body {
            padding: 10px;
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .day {
            font-size: 14px;
            padding: 8px;
            text-align: center;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .weekend {
            background-color: #ff7b54;
        }

        .weekday {
            background-color: #e0e0e0;
        }

        .festival {
            background-color: #f3c940;
        }

        [data-tooltip] {
            position: relative;
            cursor: pointer;
        }

        [data-tooltip]::after {
            content: attr(data-tooltip);
            position: absolute;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 14px;
            white-space: nowrap;
            z-index: 100;
            bottom: calc(100% + 5px);
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        [data-tooltip]:hover::after {
            opacity: 1;
        }
    </style>
</head>
<body>

    <div class="container">
        <img src="dashboardicon.png" alt="Background Image">
    </div>
    <h2 class="centered_header">Attendance Dashboard</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="date">Select Date:</label>
        <input type="date" id="date" name="date" required>

        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="all">All</option>
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
        </select>

        <input type="submit" value="Filter">
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <?php if (!empty($attendanceRecords)): ?>
            <h3>Attendance for <?php echo htmlspecialchars($selectedDate); ?></h3>
            <table>
                <tr>
                    <th>Employee ID</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($attendanceRecords as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['employee_id']); ?></td>
                        <td><?php echo htmlspecialchars($record['date_time']); ?></td>
                        <td><?php echo htmlspecialchars($record['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No attendance records found<?php if (isset($selectedDate)) echo " for " . htmlspecialchars($selectedDate); ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <div class="container">
        <div class="indicators">
            <div class="indicator weekend">Weekend</div>
            <div class="indicator weekday">Weekday</div>
            <div class="indicator holiday">Holiday</div>
        </div>
        <div class="calendar-container">
            <h2>Employee Calendar</h2>
            <div id="yearly-calendar">
                <div class="calendar-scroll">
                    <div class="scroll-left">&lt;</div>
                    <div class="scroll-right">&gt;</div>
                    <div class="calendar-content"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="dashboard1.js"></script>

</body>
</html>
