<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Database connection
$db = new mysqli("localhost", "root", "", "kihungwe");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Fetch user profile information
$query_profile = "SELECT * FROM users WHERE id = {$_SESSION['user_id']}";
$result_profile = $db->query($query_profile);

if ($result_profile->num_rows > 0) {
    $profile_data = $result_profile->fetch_assoc();
} else {
    header("Location: error_page.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Add your CSS styles here if needed -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        main {
            width: 80%;
            margin: 20px auto;
            padding: 15px;
            box-sizing: border-box;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        p {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <header>
        <h1>User Profile</h1>
    </header>

    <main>
        <h2>User Profile</h2>
        <?php
        echo "<p>Username: {$profile_data['username']}</p>";

        // Check if 'email' key exists in the array
        if (isset($profile_data['email'])) {
            echo "<p>Email: {$profile_data['email']}</p>";
        } else {
            echo "<p>Email: Not available</p>";
        }
        ?>
    </main>
</body>
</html>
