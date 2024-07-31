<?php
require_once 'mongod_connect.php'; // Include MongoDB connection script

header('Content-Type: application/json'); // Set content type to JSON

$response = [];

// Retrieve form data
$name = $_POST['name'];
$date = $_POST['date'];
$status = $_POST['status'];

// MongoDB collection
$collection = $db->attendance; // Replace 'attendance' with your collection name

// Data to insert into MongoDB
$data = [
    'name' => $name,
    'date' => new MongoDB\BSON\UTCDateTime(strtotime($date) * 1000), // Convert date to MongoDB date format
    'status' => $status
];

// Insert data into MongoDB
try {
    $insertOneResult = $collection->insertOne($data);
    $response['success'] = true;
    $response['insertedId'] = (string) $insertOneResult->getInsertedId();
} catch (MongoDB\Driver\Exception\Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
