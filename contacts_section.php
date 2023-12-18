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

// Handle form submission to add a new contact
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_contact"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $contactNumber = $_POST["contact_number"];

    // Sanitize inputs
    $name = $db->real_escape_string(htmlspecialchars(trim($name)));
    $email = $db->real_escape_string(htmlspecialchars(trim($email)));
    $contactNumber = $db->real_escape_string(htmlspecialchars(trim($contactNumber)));

    // Insert the new contact
    $queryInsertContact = "INSERT INTO contacts (user_id, name, email, contact_number, created_at) 
                           VALUES ({$_SESSION['user_id']}, '$name', '$email', '$contactNumber', NOW())";

    $resultInsertContact = $db->query($queryInsertContact);

    if ($resultInsertContact) {
        // Get the ID of the newly added contact
        $newContactId = $db->insert_id;

        // Check if a user is selected to share the contact
        if (isset($_POST["receiver_id"])) {
            $receiverId = $_POST["receiver_id"];

            // Insert the shared contact entry
            $queryShareContact = "INSERT INTO shared_contacts (contact_id, receiver_id) 
                                  VALUES ($newContactId, $receiverId)";

            $resultShareContact = $db->query($queryShareContact);

            if (!$resultShareContact) {
                echo "Failed to share the contact.";
            }
        }

        // Save the contact to the user's contacts_section
        saveToContactsSection($db, $_SESSION['user_id'], $newContactId);

        header("Location: contacts_section.php");
        exit();
    } else {
        echo "Failed to add the contact.";
    }
}

// Fetch and display contacts and shared contacts for the logged-in user
$userId = $_SESSION['user_id'];
$queryContacts = "SELECT * FROM contacts WHERE user_id = $userId";
$resultContacts = $db->query($queryContacts);

$querySharedContacts = "SELECT c.* FROM contacts c
                        JOIN shared_contacts s ON c.id = s.contact_id
                        WHERE s.receiver_id = $userId";
$resultSharedContacts = $db->query($querySharedContacts);

// Function to save shared contact to contacts_section
function saveToContactsSection($db, $userId, $contactId)
{
    $querySaveToContactsSection = "INSERT INTO contacts_section (user_id, contact_id) VALUES ($userId, $contactId)";
    $resultSaveToContactsSection = $db->query($querySaveToContactsSection);

    if (!$resultSaveToContactsSection) {
        echo "Failed to save contact to contacts section. Please try again. Error: " . $db->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts Section</title>

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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        a.action-link {
            text-decoration: none;
            color: #007bff;
            margin-right: 10px;
        }

        a.action-link:hover {
            text-decoration: underline;
        }

        p.no-contacts {
            color: #888;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <main>
        <h2>All Contacts</h2>

        <?php if (($resultContacts && $resultContacts->num_rows > 0) || ($resultSharedContacts && $resultSharedContacts->num_rows > 0)) : ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact Number</th>
                    <th>Actions</th> <!-- New column for actions -->
                </tr>

                <?php
                if ($resultContacts && $resultContacts->num_rows > 0) {
                    while ($row = $resultContacts->fetch_assoc()) {
                        $contactId = $row['id'];
                        $contactName = $row['name'];
                        $contactNumber = $row['contact_number'];

                        echo "<tr>";
                        echo "<td>$contactId</td><td>$contactName</td><td>$contactNumber</td>";
                        echo "<td>";
                        echo "<a class='action-link' href='edit_contact.php?contact_id=$contactId'>Edit</a>";
                        echo "<a class='action-link' href='delete_contact.php?contact_id=$contactId'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";

                        // Save the user's contacts to contacts_section
                        saveToContactsSection($db, $userId, $contactId);
                    }
                }

              if ($resultContacts && $resultContacts->num_rows > 0) {
    while ($row = $resultContacts->fetch_assoc()) {
        $contactId = $row['id'];
        $contactName = $row['name'];
        $contactNumber = $row['contact_number'];

        echo "<tr>";
        echo "<td>$contactId</td><td>$contactName</td><td>$contactNumber</td>";
        echo "<td>";
        echo "<a class='action-link' href='edit_contact.php?id=$contactId'>Edit</a>";
        echo "<a class='action-link' href='delete_contact.php?id=$contactId'>Delete</a>";
        echo "</td>";
        echo "</tr>";

        // Save the user's contacts to contacts_section
        saveToContactsSection($db, $userId, $contactId);
    }
}

if ($resultSharedContacts && $resultSharedContacts->num_rows > 0) {
    while ($row = $resultSharedContacts->fetch_assoc()) {
        $contactId = $row['id'];
        $contactName = $row['name'];
        $contactNumber = $row['contact_number'];

        echo "<tr>";
        echo "<td>$contactId</td><td>$contactName</td><td>$contactNumber</td>";
        echo "<td>";
        echo "<a class='action-link' href='edit_contact.php?id=$contactId'>Edit</a>";
        echo "<a class='action-link' href='delete_contact.php?id=$contactId'>Delete</a>";
        echo "</td>";
        echo "</tr>";

        // Save the shared contacts to contacts_section
        saveToContactsSection($db, $userId, $contactId);
    }
}
?>
            </table>
        <?php else : ?>
            <p class="no-contacts">No contacts available.</p>
        <?php endif; ?>
    </main>
</body>

</html>
