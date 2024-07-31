<?php
require 'vendor/autoload.php'; // Include Composer's autoloader

use MongoDB\Client as MongoClient;

session_start(); // Start the session

// Check if session exists
if (isset($_SESSION['username'])) {
    // Redirect to appropriate page based on role
    if ($_SESSION['role'] == 'admin') {
        header("Location: adlacadmin.php");
        exit();
    } else {
        header("Location: adlacuser.php");
        exit();
    }
}

// Initialize error message
$errorMessage = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $client = new MongoClient("mongodb://localhost:27017");
        $collection = $client->user_management->users;

        $user = $collection->findOne(['username' => $username]);

        if ($user && password_verify($password, $user['password'])) {
            // Start a session and store user information
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            $_SESSION['employeeId'] = $user['employeeId'];

            // Redirect to appropriate page based on role
            if ($user['role'] == 'admin') {
                header("Location: adlacadmin.php");
            } else {
                header("Location: adlacuser.php");
            }
            exit();
        } else {
            $errorMessage = "Invalid username or password.";
        }
    } catch (Exception $e) {
        $errorMessage = "Error: Could not connect to MongoDB: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css">
       
        <style>
        /* Your CSS code here */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background: url('loginback1.jpg') center/cover no-repeat fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333333;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            max-width: 90%;
        }
        .login-container form {
            max-width: 100%;
        }
        .login-container h2 {
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 600;
            color: #444444;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        .login-container img {
            width: 140px;
            height: 120px;
            border-radius: 50%;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #666666;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }
        .input-group input {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            background-color: rgba(255, 255, 255, 0.8);
            transition: background-color 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            outline: none;
            border: 1px solid #ccc;
        }
        .input-group input:focus {
            background-color: #ffffff;
            border-color: #666666;
        }
        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 25px;
            background-color: #4b6cb7;
            color: #ffffff;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 6px rgba(75, 108, 183, 0.3);
        }
        button:hover {
            background-color: #182848;
        }
        ::placeholder {
            opacity: 0.7;
            color: #aaaaaa;
        }
        @media (max-width: 768px) {
            .login-container {
                padding: 20px;
            }
            .login-container img {
                width: 80px;
                height: 80px;
            }
            .login-container h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <form action="login.php" method="POST">
            <h2>Login</h2>
            <img src="login.jpeg" alt="Login Image">
            <div class="input-group">
                <label for="username">Name</label>
                <input placeholder="Enter your Name" type="text" id="username" name="username" required>
            </div>
            <br>
            <div class="input-group">
                <label for="password">Password:</label>
                <input placeholder="Enter your Password" type="password" id="password" name="password" required>
            </div>
            <br><br>
            <button type="submit">Login</button>
        </form>
    </div>

    <?php
    if ($errorMessage) {
        echo "<script>alert('" . addslashes($errorMessage) . "');</script>";
    }
    ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function typewriterEffect(element, text, delay) {
                let index = 0;
                function type() {
                    if (index < text.length) {
                        element.setAttribute("placeholder", text.substring(0, index + 1));
                        index++;
                        setTimeout(type, delay);
                    }
                }
                type();
            }

            const usernameInput = document.getElementById("username");
            const passwordInput = document.getElementById("password");

            typewriterEffect(usernameInput, "Enter your Username", 100);
            setTimeout(() => typewriterEffect(passwordInput, "Enter your Password", 100), 1500); // Delay starting the password placeholder animation
        });
    </script>
</body>
</html>
