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

// Add new contact function
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

// Handle add contact form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_contact"])) {
    $name = sanitize_input($_POST["name"]);
    $contactNumber = sanitize_input($_POST["contact_number"]);
    $email = sanitize_input($_POST["email"]);

    addNewContact($db, $_SESSION['user_id'], $name, $contactNumber, $email);
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
    </style>
</head>

<body>
   
    <main>
        <h2>Fill in the details to add a new contact:</h2>

        <!-- Add New Contact Form -->
        <form method="post" action="add_contact.php">
            <label for="name">Name:</label>
            <input type="text" name="name" required>

            <label for="contact_number">Contact Number:</label>
            <input type="text" name="contact_number" required>

            <label for="email">Email:</label>
            <input type="text" name="email" required>

            <input type="submit" name="add_contact" value="Add Contact">
        </form>
    </main>

