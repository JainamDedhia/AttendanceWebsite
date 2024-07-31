
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sign-styles.css">
    <title>Sign Up Page</title>
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
            border-radius: 50px;
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
   
        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .input-group input,
        .input-group select {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 25px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .input-group select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position-x: calc(100% - 10px);
            background-position-y: center;
            background-size: 18px;
            padding-right: 30px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <form id="signup-form" action="signup.php" method="POST">
        <img src="signup.jpg" alt="Sign Up Image">
        <div class="input-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>
        </div>
        <div class="input-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="input-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <div class="input-group">
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <br>
        <button type="submit">Sign Up</button>
    </form>
    <div class="login-link">
        <p>Already have an account? <a href="login.php">Click here to login</a></p>
    </div>

    <?php
    require 'vendor/autoload.php'; // Include Composer's autoloader
    
    use MongoDB\Client as MongoClient;
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $role = $_POST['role'];
    
        try {
            $client = new MongoClient("mongodb://localhost:27017");
            $collection = $client->user_management->users;
    
            // Check for duplicate username
            $existingUser = $collection->findOne(['username' => $username]);
            if ($existingUser) {
                echo "<div class='error-message'>Username already exists. Please choose a different username.</div>";
                exit();
            }
    
            // Check for duplicate email
            $existingEmail = $collection->findOne(['email' => $email]);
            if ($existingEmail) {
                echo "<div class='error-message'>Email already exists. Please choose a different email.</div>";
                exit();
            }

            // Generate a unique employee ID
            do {
                $employeeId = 'EMP' . rand(1000, 9999);
                $existingId = $collection->findOne(['employeeId' => $employeeId]);
            } while ($existingId);

            $user = [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => $role,
                'employeeId' => $employeeId // Adding employeeId to the user document
            ];
    
            $insertResult = $collection->insertOne($user);
    
            if ($insertResult->getInsertedCount() > 0) {
                echo "<div class='success-message'>Registration successful! Welcome, $username.</div>";
                // Trigger JavaScript to show the modal
                echo "<script>document.addEventListener('DOMContentLoaded', function() {
                          setEmployeeId('$employeeId');
                      });</script>";
            } else {
                echo "<div class='error-message'>Error: Could not sign up.</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error-message'>Error: Could not connect to MongoDB: " . $e->getMessage() . "</div>";
        }
    }
    ?>

</div>

<!-- The Modal -->
<div id="signupSuccessModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Thank you for signing up!</h2>
        <p>Your registration was successful.</p>
        <p>Your ID is <span id="employeeId">__</span>.</p>
        <p>You can now log in using your credentials.</p>
    </div>
</div>

<script>
    // Function to close the modal
    function closeModal() {
        document.getElementById('signupSuccessModal').style.display = 'none';
    }

    // Function to set the Employee ID
    function setEmployeeId(id) {
        document.getElementById('employeeId').textContent = id;
        document.getElementById('signupSuccessModal').style.display = 'block';
    }

    // Typewriter effect for input placeholders
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
        const emailInput = document.getElementById("email");
        const passwordInput = document.getElementById("password");

        typewriterEffect(usernameInput, "Enter your username", 100);
        setTimeout(() => typewriterEffect(emailInput, "Enter your email", 100), 2000); // Delay for next placeholder animation
        setTimeout(() => typewriterEffect(passwordInput, "Enter your password", 100), 4000); // Delay for next placeholder animation
    });
</script>

</body>
</html>
