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

// Function to sanitize user inputs
function sanitize_input($input) {
    global $db; // Use the global $db variable
    return mysqli_real_escape_string($db, trim($input));
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $request_id = sanitize_input($_GET["id"]);

    // Get friend request details
    $query_request = "SELECT * FROM friend_requests WHERE id = $request_id AND receiver_id = {$_SESSION['user_id']} AND status = 'pending'";
    $result_request = $db->query($query_request);

    if ($result_request->num_rows > 0) {
        // Update friend request status to rejected
        $query_reject = "UPDATE friend_requests SET status = 'rejected' WHERE id = $request_id";
        $result_reject = $db->query($query_reject);

        if ($result_reject) {
            $notification = "Friend request rejected successfully.";
        } else {
            $notification = "Failed to reject friend request. Please try again.";
        }
    } else {
        $notification = "Invalid friend request.";
    }
    
    
    header("Location: friend_requests.php?notification=" . urlencode($notification));
    exit();
} else {
    echo "Invalid request.";
}
?>
