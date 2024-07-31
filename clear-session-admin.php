<?php
session_start();

require 'vendor/autoload.php'; // Include Composer's autoloader

use MongoDB\Client as MongoClient;

// Check if the user is logged in
if (isset($_SESSION['username'],$_SESSION['role'])) {
    $username = $_SESSION['username'];
    $role = $_SESSION['role'];

    // Get the current checkout time
    $checkoutTime = date('H:i:s');

    try {
        // Connect to MongoDB
        $client = new MongoClient("mongodb://localhost:27017");
        $collection = $client->user_management->checkout_times;

        // Insert the checkout time into the database
        $result = $collection->insertOne([
            'username' => $username,
            'role' => $role,
            'checkout_time' => $checkoutTime,
            'date' => new MongoDB\BSON\UTCDateTime() // Store the current date and time
        ]);

        // Clear the session
        session_unset();
        session_destroy();

        // Respond with a success message
        echo json_encode(["status" => "success"]);
    } catch (Exception $e) {
        // Respond with an error message if there's an exception
        echo json_encode(["status" => "error", "message" => "Failed to save checkout time: " . $e->getMessage()]);
    }
} else {
    // Respond with an error message if no user is logged in
    echo json_encode(["status" => "error", "message" => "No user is logged in."]);
}
?>
