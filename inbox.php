<?php
session_start();

$db = new mysqli("localhost", "root", "", "kihungwe");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$userId = $_SESSION['user_id'];

$query = "SELECT m.*, u.username AS sender_username FROM messages m
          JOIN users u ON m.sender_id = u.id
          WHERE m.receiver_id = $userId
          ORDER BY m.created_at DESC";

$result = $db->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <!-- Add your CSS styles here -->
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
        <h1>Inbox</h1>
    </header>

    <main>
        <h2>Received Messages</h2>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<p>From: {$row['sender_username']}</p>";
                echo "<p>Message: {$row['message']}</p>";
                echo "<p>Sent at: {$row['created_at']}</p>";
                echo "<hr>";
            }
        } else {
            echo "<p>No messages in the inbox.</p>";
        }
        ?>
    </main>
</body>

</html>
