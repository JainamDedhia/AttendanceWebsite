<?php
require 'vendor/autoload.php';

use MongoDB\Client as MongoClient;

try {
    $mongoClient = new MongoClient('mongodb://localhost:27017');
    echo "Connected to MongoDB successfully.";
} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}
?>
