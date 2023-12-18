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

// Fetch and display shared contacts
$query_shared_contacts = "SELECT c.*, u.username AS friend_username FROM contacts c
                          JOIN shared_contacts s ON c.id = s.contact_id
                          JOIN users u ON s.receiver_id = u.id
                          WHERE c.user_id = {$_SESSION['user_id']}";
$result_shared_contacts = $db->query($query_shared_contacts);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Contacts</title>
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

        h1 {
            color: #fff;
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
        <h1>Contacts You Have Shared</h1>
    </header>

    <main>
        <h2>Shared Contacts</h2>
        <?php
        if ($result_shared_contacts->num_rows > 0) {
            while ($row_shared_contact = $result_shared_contacts->fetch_assoc()) {
                $friend_username = $row_shared_contact['friend_username'];
                echo "<p>Friend: $friend_username, Name: {$row_shared_contact['name']}</p>";
            }
        } else {
            echo "<p>No shared contacts.</p>";
        }
        ?>
    </main>
</body>

</html>
