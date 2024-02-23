<?php
session_start();

// Database connection
$db = new mysqli("localhost", "root", "", "kihungwe");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Your logic for displaying a list of users
$queryUsers = "SELECT * FROM users WHERE id != {$_SESSION['user_id']}";
$resultUsers = $db->query($queryUsers);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin-bottom: 10px;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <header>
        <h1>View Users</h1>
    </header>

    <main>
        <h2>Users List</h2>
        <ul>
            <?php
            while ($rowUser = $resultUsers->fetch_assoc()) {
                echo "<li>{$rowUser['username']} - <a href='send_request.php?receiver={$rowUser['username']}'>Send Friend Request</a></li>";
            }
            ?>
        </ul>
    </main>
</body>

</html>
