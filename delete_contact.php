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

// Get contact ID from the URL parameter
$contactId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Delete related rows in shared_contacts
$queryDeleteSharedContacts = "DELETE FROM shared_contacts WHERE contact_id = $contactId";
$resultDeleteSharedContacts = $db->query($queryDeleteSharedContacts);

// Check for success before deleting the contact
if ($resultDeleteSharedContacts) {
    // Proceed with deleting the contact from the contacts table
    $queryDeleteContact = "DELETE FROM contacts WHERE id = $contactId";
    $resultDeleteContact = $db->query($queryDeleteContact);

    if ($resultDeleteContact) {
        header("Location: contacts_section.php");
        exit();
    } else {
        echo "Failed to delete contact. Please try again.";
    }
} else {
    echo "Failed to delete related shared contacts. Please try again.";
}
?>
