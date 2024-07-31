<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include Composer's autoload file if MongoDB library is used via Composer
require 'vendor/autoload.php';

use MongoDB\Client as MongoClient;

// MongoDB connection string and options
$mongoURI = 'mongodb://localhost:27017';
$options = [];

// Create a MongoDB client
try {
    $mongoClient = new MongoClient($mongoURI, $options);
} catch (Exception $e) {
    echo json_encode(["message" => "Failed to connect to MongoDB: " . $e->getMessage()]);
    exit;
}

// Select your database and collection
$databaseName = 'attendancetrial';
$collectionName = 'attendance';
try {
    $database = $mongoClient->$databaseName;
    $collection = $database->$collectionName;
} catch (Exception $e) {
    echo json_encode(["message" => "Failed to select database or collection: " . $e->getMessage()]);
    exit;
}

// Receive latitude and longitude from frontend
if (!isset($_POST['latitude']) || !isset($_POST['longitude']) || !isset($_POST['id']) || !isset($_POST['dob'])) {
    echo json_encode(["message" => "Invalid input."]);
    exit;
}

$employeeLatitude = (float) $_POST['latitude'];
$employeeLongitude = (float) $_POST['longitude'];
$id = $_POST['id'];
$dob = $_POST['dob'];

// Designated location coordinates (for example, office location)
$designatedLatitude = 19.0946357;
$designatedLongitude = 72.8906734;

// Calculate distance using Haversine formula
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371.0; // Radius of the Earth in kilometers

    // Convert latitude and longitude from degrees to radians
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    // Haversine formula
    $dLat = $lat2 - $lat1;
    $dLon = $lon2 - $lon1;

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos($lat1) * cos($lat2) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Distance in meters
    $distance = $earthRadius * $c * 1000;

    return $distance;
}

$distance = calculateDistance($designatedLatitude, $designatedLongitude, $employeeLatitude, $employeeLongitude);

// Get the current date and time
$currentDateTime = new DateTime();

// Check if an attendance record exists for this employee and date
$existingRecord = $collection->findOne(['employee_id' => $id, 'date_time' => $dob]);

if ($existingRecord) {
    if ($distance <= 100) {
        if (isset($existingRecord['exit_time']) && !isset($existingRecord['return_time'])) {
            // Employee is returning to the designated location
            $collection->updateOne(
                ['employee_id' => $id, 'date_time' => $dob],
                ['$set' => ['return_time' => $currentDateTime->format('Y-m-d H:i:s')]]
            );
            echo json_encode(["message" => "Employee has returned to the designated location. Return time recorded."]);
        } else {
            echo json_encode(["message" => "Employee is within the designated location. Attendance record is intact."]);
        }
    } else {
        if (!isset($existingRecord['exit_time'])) {
            // Employee is leaving the designated location
            $collection->updateOne(
                ['employee_id' => $id, 'date_time' => $dob],
                ['$set' => ['exit_time' => $currentDateTime->format('Y-m-d H:i:s')]]
            );
            echo json_encode(["message" => "Employee is outside the designated location. Exit time recorded."]);
        } else {
            echo json_encode(["message" => "Employee is outside the designated location. Exit time already recorded."]);
        }
    }
    
    // Start and stop timer logic
    $timerAction = $_POST['timerAction'] ?? '';

    if ($timerAction === 'start') {
        startTimer();
    } elseif ($timerAction === 'stop') {
        stopTimer();
    }
} else {
    echo json_encode(["message" => "No existing attendance record found."]);
}

// Function to start the timer
function startTimer() {
    global $currentDateTime, $collection, $id, $dob;
    
    $startTime = $currentDateTime->format('Y-m-d H:i:s');
    $collection->updateOne(
        ['employee_id' => $id, 'date_time' => $dob],
        ['$set' => ['start_time' => $startTime]]
    );
    echo json_encode(["message" => "Timer started at: " . $startTime]);
}

// Function to stop the timer
function stopTimer() {
    global $currentDateTime, $collection, $id, $dob;
    
    $stopTime = $currentDateTime->format('Y-m-d H:i:s');
    $collection->updateOne(
        ['employee_id' => $id, 'date_time' => $dob],
        ['$set' => ['stop_time' => $stopTime]]
    );
    echo json_encode(["message" => "Timer stopped at: " . $stopTime]);
}
?>
