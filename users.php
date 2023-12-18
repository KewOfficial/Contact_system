<?php
session_start();

// Database connection
$db = new mysqli("localhost", "root", "", "kihungwe");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Logic for displaying a list of logged-in users
$queryUsers = "SELECT * FROM users WHERE id != {$_SESSION['user_id']}";
$resultUsers = $db->query($queryUsers);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            align-items: center;
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
            margin-top: 20px;
            padding: 15px;
            box-sizing: border-box;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        ul.user-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        ul.user-list li {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        ul.user-list a {
            text-decoration: none;
            color: #333;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        ul.user-list a:hover {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        // Function to show the pop-up message
        function showPopup(message) {
            alert(message);
        }
    </script>
</head>

<body>
    <header>
        <h1>Contact Management System</h1>
    </header>

    <main>
        <h2>Connect with Friends</h2>
        <ul class="user-list">
            <?php
            while ($rowUser = $resultUsers->fetch_assoc()) {
                $receiverId = $rowUser['id'];
                echo "<li>{$rowUser['username']} - <a href='send_request.php?receiver={$receiverId}' onclick='showPopup(\"Friend request sent successfully.\")'>Send Friend Request</a></li>";
            }
            ?>
        </ul>
    </main>
</body>

</html>
