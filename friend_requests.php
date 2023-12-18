<?php
// Start the session
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

if (isset($_GET['notification'])) {
    echo "<p>{$_GET['notification']}</p>";
}

// Handle friend request actions (accept/reject)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"]) && isset($_GET["id"])) {
    $requestId = sanitize_input($_GET["id"]);

    // Fetch the friend request
    $queryFetchRequest = "SELECT fr.sender_id, fr.status, u.username FROM friend_requests fr 
                          JOIN users u ON fr.sender_id = u.id
                          WHERE fr.id = $requestId";
    $resultFetchRequest = $db->query($queryFetchRequest);

    if ($resultFetchRequest->num_rows > 0) {
        $request = $resultFetchRequest->fetch_assoc();
        $senderId = $request["sender_id"];
        $receiverId = $_SESSION["user_id"];

        // Update the status based on the action
        if ($_GET["action"] == "accept") {
            // Update status to accepted
            $queryUpdateStatus = "UPDATE friend_requests SET status = 'accepted' WHERE id = $requestId";
            $db->query($queryUpdateStatus);

            $notification = "You accepted friend request from {$request['username']}.";
        } elseif ($_GET["action"] == "reject") {
            // Update status to rejected
            $queryUpdateStatus = "UPDATE friend_requests SET status = 'rejected' WHERE id = $requestId";
            $db->query($queryUpdateStatus);

            $notification = "You rejected friend request from {$request['username']}.";
        }

        // Redirect back to friend_requests.php with notification
        header("Location: friend_requests.php?notification=$notification");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friend Requests</title>

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

        .friend-request {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }

        .friend-request-message {
            margin-bottom: 10px;
        }

        .friend-request-actions a {
            text-decoration: none;
            color: #007bff;
            margin-right: 10px;
        }

        .friend-request-actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <header>
        <h1>Friend Requests</h1>
    </header>

    <main>
        <h2>Friend Requests</h2>
        <?php
        // Display friend requests
        $query = "SELECT fr.id, fr.sender_id, fr.status, u.username FROM friend_requests fr 
                  JOIN users u ON fr.sender_id = u.id
                  WHERE fr.receiver_id = {$_SESSION['user_id']} AND fr.status = 'pending'";
        $result = $db->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='friend-request'>";
                echo "<p class='friend-request-message'>{$row['username']} wants to be your friend.</p>";
                echo "<div class='friend-request-actions'>";
                echo "<a href='friend_requests.php?action=accept&id={$row['id']}' class='friend-request-action accept'>Accept</a>";
                echo "<a href='friend_requests.php?action=reject&id={$row['id']}' class='friend-request-action reject'>Reject</a>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No friend requests.</p>";
        }
        ?>
    </main>
</body>

</html>
