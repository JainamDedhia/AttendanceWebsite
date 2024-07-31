<?php
session_start();

require 'vendor/autoload.php'; // Include Composer's autoloader

use MongoDB\Client as MongoClient;

// Check if the user is logged in
if (isset($_SESSION['username'], $_SESSION['role'], $_SESSION['employeeId'])) {
    $username = $_SESSION['username'];
    $role = $_SESSION['role'];
    $employeeId = $_SESSION['employeeId'];

    try {
        // Connect to MongoDB
        $client = new MongoClient("mongodb://localhost:27017");
        $attendanceCollection = $client->attendancetrial->attendance;

        // Calculate total time spent outside
        $attendanceRecords = $attendanceCollection->find(['employee_id' => $employeeId]);
        $totalOutsideTime = 0;

        foreach ($attendanceRecords as $record) {
            if (isset($record['exit_time'], $record['return_time'])) {
                $exitTime = new DateTime($record['exit_time']);
                $returnTime = new DateTime($record['return_time']);
                $totalOutsideTime += ($returnTime->getTimestamp() - $exitTime->getTimestamp());
            }
        }

        // Get the current checkout time
        $checkoutTime = new DateTime();

        // Adjust checkout time by subtracting the total outside time
        $adjustedCheckoutTime = clone $checkoutTime;
        $adjustedCheckoutTime->modify("-{$totalOutsideTime} seconds");

        // Insert the adjusted checkout time into the database
        $userCollection = $client->user_management->checkout_times;
        $userCollection->insertOne([
            'username' => $username,
            'role' => $role,
            'checkout_time' => $adjustedCheckoutTime->format('H:i:s'),
            'date' => new MongoDB\BSON\UTCDateTime() // Store the current date and time
        ]);

        // Clear the session
        session_unset();
        session_destroy();

        // Respond with success JSON
        echo json_encode(["status" => "success", "adjusted_checkout_time" => $adjustedCheckoutTime->format('H:i:s')]);
    } catch (Exception $e) {
        // Respond with error JSON
        http_response_code(500); // Internal Server Error
        echo json_encode(["status" => "error", "message" => "Failed to save checkout time: " . $e->getMessage()]);
    }
} else {
    // Respond with error JSON if no user is logged in
    http_response_code(403); // Forbidden
    echo json_encode(["status" => "error", "message" => "No user is logged in."]);
}
?>
