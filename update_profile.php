<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Database connection
$db = new mysqli("your_host", "your_username", "your_password", "Kihungwe");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = sanitize_input($_POST["new_username"]);
    $new_email = sanitize_input($_POST["new_email"]);
    // Update user details
    $query_update = "UPDATE users SET username = '$new_username', email = '$new_email' WHERE id = {$_SESSION['user_id']}";
    $result_update = $db->query($query_update);

    if ($result_update) {
        echo "Profile updated successfully.";
    } else {
        echo "Failed to update profile. Please try again.";
    }
}
?>
