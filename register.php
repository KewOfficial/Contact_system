<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}

// Database connection
$db = new mysqli("localhost", "root", "", "kihungwe");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Function to sanitize user inputs
function sanitize_input($input)
{
    return htmlspecialchars(stripslashes(trim($input)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a user is already logged in
    if (isset($_SESSION["user_id"])) {
        echo "You are already logged in. Please log out first.";
        exit();
    }

    // Get user inputs
    $username = sanitize_input($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    
    // Check if optional fields are set
    $phone = isset($_POST["phone"]) ? sanitize_input($_POST["phone"]) : '';
    $email = isset($_POST["email"]) ? sanitize_input($_POST["email"]) : '';

    // Check for duplicate email
    $check_query = "SELECT id FROM users WHERE email = '$email'";
    $check_result = $db->query($check_query);

    if ($check_result->num_rows > 0) {
        echo "Email is already registered. Please use a different email.";
    } else {
        // Insert user into the database
        $query = "INSERT INTO users (username, password, phone, email) VALUES ('$username', '$password',  '$phone', '$email')";
        $result = $db->query($query);

        if ($result) {
            header("Location: login.php");
            exit();
        } else {
            echo "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #2c3e50; 
            color: #ecf0f1; 
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        form {
            max-width: 400px;
            margin: auto;
            background-color: #34495e; 
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        h2 {
            text-align: center;
            color: #ecf0f1;
            margin-bottom: 30px;
            font-size: 28px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #bdc3c7; 
            font-size: 16px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            box-sizing: border-box;
            border: 1px solid #bdc3c7;
            border-radius: 6px;
            font-size: 16px;
            background-color: #ecf0f1;
            color: #2c3e50; 
        }

        input[type="submit"] {
            background-color: #27ae60; 
            color: #ecf0f1;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #219a52;
        }
    </style>
</head>
<body>
    <form method="post" action="">
        <h2>Register Now</h2>
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <label for="phone">Phone Number:</label>
        <input type="text" name="phone" required>
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <input type="submit" value="Register">
    </form>
</body>
</html>
