<?php
session_start();

// Checking if the user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Database connection
$db = new mysqli("localhost", "root", "", "kihungwe");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

function requestExists($db, $user1, $user2)
{
    $query = "SELECT * FROM friends
              WHERE (user1_id = $user1 AND user2_id = $user2)
                 OR (user1_id = $user2 AND user2_id = $user1)
                 AND status = 'pending'";
    $result = $db->query($query);

    return ($result && $result->num_rows > 0);
}

function acceptRequest($db, $user1, $user2)
{
    $query = "UPDATE friends SET status = 'accepted'
              WHERE (user1_id = $user1 AND user2_id = $user2)
                 OR (user1_id = $user2 AND user2_id = $user1)";
    $result = $db->query($query);

    return ($result !== false);
}

// Handling accept friend request form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accept_request"])) {
    $friendUsername = htmlspecialchars(trim($_POST["friend_username"]));

    // Getting  friend's user ID
    $queryFriendId = "SELECT id FROM users WHERE username = '$friendUsername'";
    $resultFriendId = $db->query($queryFriendId);

    if ($resultFriendId && $resultFriendId->num_rows > 0) {
        $friendId = $resultFriendId->fetch_assoc()['id'];

        // Checking if a friend request exists
        if (requestExists($db, $_SESSION['user_id'], $friendId)) {
            if (acceptRequest($db, $_SESSION['user_id'], $friendId)) {
                echo "Friend request accepted successfully.";
            } else {
                echo "Failed to accept friend request. Please try again.";
            }
        } else {
            echo "No friend request found.";
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
    <title>Accept Friend Request</title>
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

        nav {
            background: #333;
            padding: 15px;
            color: #fff;
            box-sizing: border-box;
            min-height: 100vh;
            width: 20%;
            display: flex;
            flex-direction: column;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        nav ul li {
            margin-bottom: 10px;
        }

        nav a {
            text-decoration: none;
            color: #fff;
            display: flex;
            align-items: center;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #555;
        }

        nav i {
            margin-right: 8px;
        }

        main {
            width: 80%;
            padding: 20px;
            box-sizing: border-box;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            max-width: 300px;
        }

        label {
            margin-bottom: 8px;
            color: #333;
        }

        input {
            padding: 8px;
            margin-bottom: 12px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <header>
       
    </header>

    <nav>
       
    </nav>

    <main>
        <h2>Accept Friend Request</h2>

        <!-- Accept Friend Request Form -->
        <form method="post" action="accept_request.php">
            <label for="friend_username">Friend's Username:</label>
            <input type="text" name="friend_username" required>

            <input type="submit" name="accept_request" value="Accept Friend Request">
        </form>

    </main>

    <footer>
       
    
    </footer>
</body>

</html>
