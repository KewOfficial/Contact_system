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

// Sanitize user inputs
function sanitize_input($input)
{
    global $db;
    return mysqli_real_escape_string($db, trim($input));
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["receiver"])) {
    $receiverId = sanitize_input($_GET["receiver"]);

    // Check if the receiver user exists
    $queryReceiver = "SELECT username FROM users WHERE id = $receiverId";
    $resultReceiver = $db->query($queryReceiver);

    if ($resultReceiver->num_rows > 0) {
        $receiverUsername = $resultReceiver->fetch_assoc()["username"];
        $senderId = $_SESSION["user_id"];

        // Check if a friend request already exists
        $queryExistingRequest = "SELECT id FROM friend_requests 
                                WHERE sender_id = $senderId AND receiver_id = $receiverId";
        $resultExistingRequest = $db->query($queryExistingRequest);

        if ($resultExistingRequest->num_rows == 0) {
            // Send friend request
            $querySendRequest = "INSERT INTO friend_requests (sender_id, receiver_id, status) 
                                VALUES ($senderId, $receiverId, 'pending')";
            $db->query($querySendRequest);

            $notification = "Friend request sent to $receiverUsername.";
        } else {
            $notification = "Friend request already sent.";
        }
    } else {
        $notification = "User not found.";
    }

    header("Location: users.php?notification=$notification");
    exit();
} else {
    header("Location: users.php");
    exit();
}
?>
