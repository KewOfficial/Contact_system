<?php
session_start();

// Include common functions and database connection
require_once 'common.php';

// Database connection
$db = new mysqli("localhost", "root", "", "kihungwe");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Function to sanitize user input
function sanitize_input($input)
{
    global $db;
    return htmlspecialchars(stripslashes(trim($db->real_escape_string($input))));
}

// Function to get user ID by username
function getUserIdByUsername($db, $username)
{
    $query = "SELECT id FROM users WHERE username = '$username'";
    $result = $db->query($query);

    return ($result->num_rows > 0) ? $result->fetch_assoc()['id'] : null;
}

// Messaging a friend and requesting a contact
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["friend_username"], $_POST["message"])) {
    $friendUsername = sanitize_input($_POST["friend_username"]);
    $message = sanitize_input($_POST["message"]);

    // Get friend's user ID
    $friendId = getUserIdByUsername($db, $friendUsername);

    if ($friendId) {
        // Send a message to the friend
        $querySendMessage = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ({$_SESSION['user_id']}, $friendId, '$message')";
        $resultSendMessage = $db->query($querySendMessage);

        if ($resultSendMessage) {
            echo "Message sent successfully.";
        } else {
            echo "Failed to send message. Please try again.";
        }
    } else {
        echo "Friend not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Friend</title>
    <!-- Add your CSS styles here -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            min-height: 100vh;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }

        main {
            width: 80%;
            padding: 20px;
            box-sizing: border-box;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2,
        h3 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            max-width: 300px;
        }

        label {
            margin-top: 10px;
        }

        input[type="text"],
        textarea,
        input[type="submit"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        textarea {
            resize: vertical;
        }

        input[type="submit"] {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
   
    <main>
        <h2>Send a Message to a Friend</h2>

        <!-- Message Form -->
        <form method="post" action="message_friend.php">
            <label for="friend_username">Friend's Username:</label>
            <input type="text" name="friend_username" required>

            <label for="message">Message:</label>
            <textarea name="message" rows="4" required></textarea>

            <input type="submit" name="send_message" value="Send Message">
        </form>
    </main>
</body>

</html>
