<?php
// MongoDB connection (assuming you have the MongoDB PHP library installed)
require 'vendor/autoload.php'; // Include Composer's autoloader

session_start();

// Check if the user is logged in and is an admin


// Check if the user is not logged in or is not an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect unauthorized users to the login page
    header("Location: login.php");
    exit(); // Stop further execution
}




use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// MongoDB connection
$mongoClient = new Client('mongodb://localhost:27017');
$database = $mongoClient->leave_management;
$collection = $database->leave_applications;

// Handling form submission for approving or rejecting leave requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        // Approve leave request
        $request_id = $_POST['request_id'];
        $updateResult = $collection->updateOne(
            ['_id' => new ObjectId($request_id)],
            ['$set' => ['status' => 'approved']]
        );
        if ($updateResult->getModifiedCount() > 0) {
            echo "Leave request approved successfully.";
        } else {
            echo "Failed to approve leave request.";
        }
    } elseif (isset($_POST['reject'])) {
        // Reject leave request
        $request_id = $_POST['request_id'];
        $updateResult = $collection->updateOne(
            ['_id' => new ObjectId($request_id)],
            ['$set' => ['status' => 'rejected']]
        );
        if ($updateResult->getModifiedCount() > 0) {
            echo "Leave request rejected successfully.";
        } else {
            echo "Failed to reject leave request.";
        }
    }
}

// Count number of pending leave requests
$count = $collection->countDocuments(['status' => 'pending']);

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Leave Application Dashboard</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background-color: #f0f0f0; color: #333;background-image: url('dashboard2.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh; }";
echo "h2 { color: #9d4edd; text-align: center; margin-bottom: 20px; font-size: 30px; }";
echo "table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }";
echo "table, th, td { border: 2px solid #9d4edd; padding: 12px; text-align: left; }";
echo "th { background-color: #9d4edd; color: #fff; }";
echo "tr:nth-child(even) { background-color: #f9f9f9; }";
echo "tr:hover { background-color: #e4c1f9; }";
echo "form { display: inline; }";
echo "input[type=submit] { background-color: #333; color: white; border: none; padding: 8px 16px; text-align: center; text-decoration: none; display: inline-block; margin: 4px 2px; cursor: pointer; font-size: 14px; transition: background-color 0.3s ease; }";
echo "input[type=submit]:hover { background-color: #555; }";
echo "p { text-align: center; font-size: 25px;}";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<h2>Leave Application Dashboard</h2>";

// Fetch all pending leave requests
$pendingRequests = $collection->find(['status' => 'pending']);

if ($count > 0) {
    echo "<p>Total pending leave requests: " . $count . "</p>";
    echo "<table>";
    echo "<tr><th>Employee ID</th><th>Start Date</th><th>End Date</th><th>Reason</th><th>Status</th><th>Action</th></tr>";
    foreach ($pendingRequests as $request) {
        // Retrieve employee details from another collection based on employee_id
        $employee = $database->employees->findOne(['employee_id' => $request->employee_id]);
        
        echo "<tr>";
        echo "<td>" . $request->employee_id . "</td>"; // Display the employee_id entered by the user
        echo "<td>" . $request->start_date->toDateTime()->format('Y-m-d') . "</td>";
        echo "<td>" . $request->end_date->toDateTime()->format('Y-m-d') . "</td>";
        echo "<td>" . $request->reason . "</td>";
        echo "<td>" . $request->status . "</td>";
        echo "<td>";
        echo "<form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='post'>";
        echo "<input type='hidden' name='request_id' value='" . $request->_id . "'>";
        echo "<input type='submit' name='approve' value='Approve'>";
        echo "<input type='submit' name='reject' value='Reject'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No pending leave requests found.</p>";
}

echo "</body>";
echo "</html>";
?>
