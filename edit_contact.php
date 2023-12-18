<?php
// Start the session
session_start();

// Check if the user is not logged in
if (!isset($_SESSION["user_id"])) {
    // Redirect to the login page or handle the case where the user is not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$db = new mysqli("localhost", "root", "", "kihungwe");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get contact ID from the URL parameter
$contactId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch contact details
$query_contact = "SELECT * FROM contacts WHERE id = $contactId";
$result_contact = $db->query($query_contact);

if ($result_contact && $result_contact->num_rows > 0) {
    $contact = $result_contact->fetch_assoc();
    $contactName = $contact['name'];
    $contactNumber = $contact['contact_number'];
    $contactEmail = $contact['email'];
} else {
    echo "Contact not found.";
    exit();
}

// Handle form submission for contact edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_contact"])) {
    $newName = isset($_POST['new_name']) ? $db->real_escape_string($_POST['new_name']) : $contactName;
    $newContactNumber = isset($_POST['new_contact_number']) ? $db->real_escape_string($_POST['new_contact_number']) : $contactNumber;
    $newEmail = isset($_POST['new_email']) ? $db->real_escape_string($_POST['new_email']) : $contactEmail;

    // Update contact details
    $query_update_contact = "UPDATE contacts SET name = '$newName', contact_number = '$newContactNumber', email = '$newEmail' WHERE id = $contactId";
    $result_update_contact = $db->query($query_update_contact);

    if ($result_update_contact) {
        header("Location: contacts_section.php");
        exit();
    } else {
        echo "Failed to update contact. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact</title>
    <style>
        main {
            padding: 20px;
            box-sizing: border-box;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            overflow: hidden; /* Prevent content overflow */
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            max-width: 400px;
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
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
        <h2>Edit Contact</h2>

        <form method="post" action="edit_contact.php?id=<?php echo $contactId; ?>">
            <label for="new_name">Name:</label>
            <input type="text" name="new_name" value="<?php echo $contactName; ?>" required>

            <label for="new_contact_number">Contact Number:</label>
            <input type="text" name="new_contact_number" value="<?php echo $contactNumber; ?>" required>

            <label for="new_email">Email:</label>
            <input type="email" name="new_email" value="<?php echo $contactEmail; ?>">

            <input type="submit" name="edit_contact" value="Save Changes">
        </form>
    </main>
</body>
</html>
