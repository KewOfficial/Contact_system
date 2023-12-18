<?php
// Function to sanitize user inputs
if (!function_exists('sanitize_input')) {
    function sanitize_input($input, $db)
    {
        return htmlspecialchars(stripslashes(trim($db->real_escape_string($input))));
    }
}

// Function to get user ID by username
if (!function_exists('getUserIdByUsername')) {
    function getUserIdByUsername($db, $username)
    {
        $query = "SELECT id FROM users WHERE username = '$username'";
        $result = $db->query($query);

        return ($result->num_rows > 0) ? $result->fetch_assoc()['id'] : false;
    }
}

// Function to get username by user ID
if (!function_exists('getUsernameById')) {
    function getUsernameById($db, $userId)
    {
        $query = "SELECT username FROM users WHERE id = $userId";
        $result = $db->query($query);

        return ($result->num_rows > 0) ? $result->fetch_assoc()['username'] : '';
    }
}

// Function to add a new contact
if (!function_exists('addNewContact')) {
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
}

// Function to share a contact
if (!function_exists('shareContact')) {
    function shareContact($db, $contactId, $receiverId)
    {
        // Check if the contact is already shared
        $queryCheck = "SELECT * FROM shared_contacts WHERE contact_id = $contactId AND receiver_id = $receiverId";
        $resultCheck = $db->query($queryCheck);

        if ($resultCheck->num_rows == 0) {
            $queryShare = "INSERT INTO shared_contacts (contact_id, receiver_id) VALUES ($contactId, $receiverId)";
            $resultShare = $db->query($queryShare);

            if ($resultShare) {
                header("Location: contacts.php");
                exit();
            } else {
                echo "Failed to share contact. Please try again.";
            }
        } else {
            echo "Contact already shared.";
        }
    }
}

// Function to display shared contacts
if (!function_exists('displaySharedContacts')) {
    function displaySharedContacts($db, $userId)
    {
        $query = "SELECT c.*, s.receiver_id FROM contacts c
                  JOIN shared_contacts s ON c.id = s.contact_id
                  WHERE c.user_id = $userId";
        $result = $db->query($query);

        if ($result->num_rows > 0) {
            echo "<h3>Shared Contacts</h3>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Contact Number</th><th>Shared With</th></tr>";

            while ($row = $result->fetch_assoc()) {
                $contactId = $row['id'];
                $contactName = $row['name'];
                $contactNumber = $row['contact_number'];
                $sharedWith = getUsernameById($db, $row['receiver_id']);

                echo "<tr>";
                echo "<td>$contactId</td><td>$contactName</td><td>$contactNumber</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No shared contacts available.</p>";
        }
    }
}

// Add more functions and configurations as needed
?>
