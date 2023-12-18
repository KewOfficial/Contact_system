<?php

session_start();
include_once 'functions.php';

// Redirect to login page if the user is not logged in
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
    return htmlspecialchars(stripslashes(trim($db->real_escape_string($input))));
}

// Display friend requests
function displayFriendRequests($db, $userId)
{
    $query = "SELECT * FROM friend_requests WHERE receiver_id = $userId AND status = 'pending'";
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $senderId = $row['sender_id'];
            $senderUsername = getUsernameById($db, $senderId);

            echo "<div class='friend-request'>";
            echo "<p class='friend-request-message'>$senderUsername wants to be your friend.</p>";
            echo "<div class='friend-request-actions'>";
            echo "<a href='accept_request.php?id={$row['id']}' class='friend-request-action accept'>Accept</a>";
            echo "<a href='reject_request.php?id={$row['id']}' class='friend-request-action reject'>Reject</a>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No friend requests.</p>";
    }
}

// Check if the user is accepted.
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["action"])) {
    $action = $_GET["action"];
    if ($action == "accept") {
        echo "Friend request accepted successfully.";
    } elseif ($action == "reject") {
        echo "Friend request rejected successfully.";
    }
}

// Get username by user ID
function getUsernameById($db, $userId)
{
    $query = "SELECT username FROM users WHERE id = $userId";
    $result = $db->query($query);

    return ($result->num_rows > 0) ? $result->fetch_assoc()['username'] : '';
}

// Get user ID by username
function getUserIdByUsername($db, $username)
{
    $query = "SELECT id FROM users WHERE username = '$username'";
    $result = $db->query($query);

    return ($result->num_rows > 0) ? $result->fetch_assoc()['id'] : false;
}

// Send friend request function
function sendFriendRequest($db, $senderId, $receiverId)
{
    // Check if a request already exists
    if (!friendRequestExists($db, $senderId, $receiverId)) {

        $querySend = "INSERT INTO friend_requests (sender_id, receiver_id, status) VALUES ($senderId, $receiverId, 'pending')";
        $resultSend = $db->query($querySend);

        if ($resultSend) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Failed to send friend request. Please try again.";
        }
    } else {
        echo "Friend request already sent or received.";
    }
}

// Check if a friend request already exists
function friendRequestExists($db, $senderId, $receiverId)
{
    $query = "SELECT * FROM friend_requests WHERE sender_id = $senderId AND receiver_id = $receiverId";
    $result = $db->query($query);

    return $result->num_rows > 0;
}

// Handle sending friend requests
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["receiver"])) {
    $receiverUsername = sanitize_input($_POST["receiver"]);

    $receiverId = getUserIdByUsername($db, $receiverUsername);

    if ($receiverId) {
        // Send friend request
        sendFriendRequest($db, $_SESSION['user_id'], $receiverId);
    } else {
        echo "User not found.";
    }
}

// Fetch and display users
function displayUsers($db, $currentUserId)
{
    $query = "SELECT id, username FROM users WHERE id <> $currentUserId";
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        echo "<h3>Users</h3>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            $userId = $row['id'];
            $username = $row['username'];

            echo "<li><a href='send_message.php?user_id=$userId'>$username</a></li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No other users available.</p>";
    }
}

$username = getUsername($db, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-...." crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
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
            width: 200px;
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
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 20px;
            box-sizing: border-box;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        h1 {
            color: white;
            margin-bottom: 20px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        p.welcome {
            font-size: 1.2em;
            color: #333;
        }

        h3.friend-requests {
            color: #333;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        p.friend-request-message {
            margin-bottom: 10px;
        }

        a.friend-request-action {
            text-decoration: none;
            color: #007bff;
            margin-right: 10px;
        }

        a.friend-request-action:hover {
            text-decoration: underline;
        }

        h3.send-friend-request {
            color: #333;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        form.send-friend-request {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            max-width: 300px;
        }

        form.send-friend-request label {
            margin-top: 10px;
        }

        form.send-friend-request input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            margin-top: 5px;
        }

        form.send-friend-request input[type="submit"] {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        form.send-friend-request input[type="submit"]:hover {
            background-color: #0056b3;
        }

        h3.my-contacts {
            color: #333;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h3.share-contacts {
            color: #333;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        form.share-contacts {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            max-width: 300px;
        }

        form.share-contacts label {
            margin-top: 10px;
        }

        form.share-contacts input[type="text"],
        form.share-contacts input[type="email"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            margin-top: 5px;
        }

        form.share-contacts input[type="submit"] {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        form.share-contacts input[type="submit"]:hover {
            background-color: #0056b3;
        }

        h3.users {
            color: #333;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        ul.user-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        ul.user-list li {
            margin-bottom: 10px;
        }

        ul.user-list a {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        ul.user-list a:hover {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <header>
        <h1>Welcome, <?php echo $username; ?>!</h1>
    </header>
    <nav>
    <!-- Sidebar Menu -->
    <ul>
        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
        <li><a href="shared_contacts.php"><i class="fas fa-users"></i> Shared Contacts</a></li>
        <li><a href="friends.php"><i class="fas fa-user-friends"></i> Friends</a></li>
        <li><a href="friend_requests.php"><i class="fas fa-user-friends"></i> Friend Requests</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> Friendship Hub</a></li>
        <li><a href="contacts_section.php"><i class="fas fa-address-book"></i> Contacts List</a></li>
        <li><a href="add_contact.php"><i class="fas fa-user-plus"></i> Add Contact</a></li>
        <li><a href="share_contacts.php"><i class="fas fa-share-alt"></i> Share Contact</a></li>
        <li><a href="message_friend.php"><i class="fas fa-comment"></i> Message Friend</a></li>
        <li><a href="inbox.php"><i class="fas fa-inbox"></i> Inbox</a></li> <!-- Added this line -->
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>



    <main>
        <!-- Your main content goes here -->
    </main>
</body>

</html>
