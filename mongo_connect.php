<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check MongoDB Connection</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="connection-check-container">
    <h2>Check MongoDB Connection</h2>
    <?php
    require 'vendor/autoload.php'; // Include Composer's autoloader

    use MongoDB\Client as MongoClient;

    try {
        $client = new MongoClient("mongodb://localhost:27017");
        $collection = $client->user_management->users;
        echo "<p style='color:green;'>Successfully connected to MongoDB.</p>";
    } catch (Exception $e) {
        echo "<p style='color:red;'>Failed to connect to MongoDB: " . $e->getMessage() . "</p>";
    }
    ?>
</div>

</body>
</html>
