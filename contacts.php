<?php
session_start();

// Include common functions and database connection
require_once 'common.php';

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
function addNewContact($db, $userId, $name, $contactNumber, $email)
{
    $queryAddContact = "INSERT INTO contacts (user_id, name, contact_number, email) VALUES ($userId, '$name', '$contactNumber', '$email')";
    $resultAddContact = $db->query($queryAddContact);

    if ($resultAddContact) {
        header("Location: contacts.php");
        exit();
    } else {
        echo "Failed to add contact. Please try again.";
    }
}

// Share contact function
function shareContact($db, $contactId, $receiverId)
{
    // Check if the contact is already shared
    $queryCheck = "SELECT * FROM shared_contacts WHERE contact_id = $contactId AND receiver_id = $receiverId";
    $resultCheck = $db->query($queryCheck);

    if ($resultCheck->num_rows == 0) {
        $queryShare = "INSERT INTO shared_contacts (contact_id, receiver_id) VALUES ($contactId, $receiverId)";
        $resultShare = $db->query($queryShare);

        if ($resultShare) {
            // Call the function to save the shared contact to the receiver's contacts_section
            saveToContactsSection($db, $receiverId, $contactId);

            header("Location: contacts.php");
            exit();
        } else {
            echo "Failed to share contact. Please try again.";
        }
    }
}

// Function to save shared contact to contacts_section
function saveToContactsSection($db, $receiverId, $contactId)
{
    $querySaveToContactsSection = "INSERT INTO contacts_section (user_id, contact_id) VALUES ($receiverId, $contactId)";
    $resultSaveToContactsSection = $db->query($querySaveToContactsSection);

    if (!$resultSaveToContactsSection) {
        echo "Failed to save contact to contacts section. Please try again. Error: " . $db->error;
    }
}

// Display shared contacts function
function displaySharedContacts($db, $userId)
{
    $query = "SELECT c.*, s.receiver_id FROM contacts c
              JOIN shared_contacts s ON c.id = s.contact_id
              WHERE c.user_id = $userId";
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        echo "<h3>Shared Contacts</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Contact Number</th><th>Email</th><th>Shared With</th></tr>";

        while ($row = $result->fetch_assoc()) {
            $contactId = $row['id'];
            $contactName = $row['name'];
            $contactNumber = $row['contact_number'];
            $contactEmail = $row['email'];
            $sharedWith = getUsernameById($db, $row['receiver_id']);

            echo "<tr>";
            echo "<td>$contactId</td><td>$contactName</td><td>$contactNumber</td><td>$contactEmail</td><td>$sharedWith</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No shared contacts available.</p>";
    }
}

// Get username by user ID
function getUsernameById($db, $userId)
{
    $query = "SELECT username FROM users WHERE id = $userId";
    $result = $db->query($query);

    return ($result->num_rows > 0) ? $result->fetch_assoc()['username'] : '';
}

// Check if the contact already exists in the friend's contacts
function contactExistsForFriend($db, $contactId, $friendId)
{
    $queryCheck = "SELECT * FROM contacts WHERE id = $contactId AND user_id = $friendId";
    $resultCheck = $db->query($queryCheck);

    return $resultCheck->num_rows > 0;
}

// Handle add contact form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_contact"])) {
    $name = sanitize_input($_POST["name"]);
    $contactNumber = sanitize_input($_POST["contact_number"]);
    $email = sanitize_input($_POST["email"]);

    addNewContact($db, $_SESSION['user_id'], $name, $contactNumber, $email);
}

// Handle share contact form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["share_contact"])) {
    $contactId = sanitize_input($_POST["contact_id"]);
    $receiverUsername = sanitize_input($_POST["receiver_username"]);

    $receiverId = getUserIdByUsername($db, $receiverUsername);

    if ($receiverId) {
        // Check if the contact already exists in the friend's contacts
        $contactExists = contactExistsForFriend($db, $contactId, $receiverId);

        if (!$contactExists) {
            // Share the contact with the friend
            shareContact($db, $contactId, $receiverId);
        } else {
            echo "Contact already exists in friend's contacts.";
        }
    } else {
        echo "User not found.";
    }
}
?>

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

        h2,
        h3 {
            color: #333;
            margin-bottom: 20px;
        }

        p {
            color: #333;
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
        input[type="submit"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
    </style>
</head>

<body>
    <main>
        <h2>Contacts</h2>

        <!-- Add New Contact Form -->
        <h3>Add New Contact</h3>
        <form method="post" action="contacts.php">
            <label for="name">Name:</label>
            <input type="text" name="name" required>

            <label for="contact_number">Contact Number:</label>
            <input type="text" name="contact_number" required>

        
            <input type="submit" name="add_contact" value="Add Contact">
        </form>

        <!-- Share Contact Form -->
        <h3>Share Contact</h3>
        <form method="post" action="contacts.php">
            <label for="contact_id">Select Contact:</label>
            <?php
            // Fetch and display user's contacts for sharing
            $query_contacts = "SELECT * FROM contacts WHERE user_id = {$_SESSION['user_id']}";
            $result_contacts = $db->query($query_contacts);

            if ($result_contacts->num_rows > 0) {
                echo "<select name='contact_id'>";
                while ($row = $result_contacts->fetch_assoc()) {
                    $contactId = $row['id'];
                    $contactName = $row['name'];
                    echo "<option value='$contactId'>$contactName</option>";
                }
                echo "</select>";
            } else {
                echo "<p>No contacts available to share.</p>";
            }
            ?>

            <label for="receiver_username">Friend's Username:</label>
            <input type="text" name="receiver_username" required>

            <input type="submit" name="share_contact" value="Share Contact">
        </form>

        <!-- Display Shared Contacts -->
        <?php
        displaySharedContacts($db, $_SESSION['user_id']);
        ?>
    </main>
</body>

</html>
