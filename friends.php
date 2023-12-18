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

// Fetch and display friends
$query_friends = "SELECT u.username AS friend_username, f.status FROM friends f
                  JOIN users u ON f.friend_id = u.id
                  WHERE f.user_id = {$_SESSION['user_id']}";
$result_friends = $db->query($query_friends);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends</title>
    <!-- CSS styles here if needed -->
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
        <h1>Your Friends</h1>
    </header>

    <main>
        <h2>Friends</h2>
        <?php
        if ($result_friends->num_rows > 0) {
            while ($row_friend = $result_friends->fetch_assoc()) {
                $friend_username = $row_friend['friend_username'];
                $status = $row_friend['status'];
                echo "<p>Friend: $friend_username (Status: $status)</p>";
            }
        } else {
            echo "<p>No friends.</p>";
        }
        ?>
    </main>
</body>

</html>
