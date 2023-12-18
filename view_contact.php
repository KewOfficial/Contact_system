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

    // Display contact details
    echo "<h2>Contact Details</h2>";
    echo "<p><strong>Name:</strong> $contactName</p>";
    echo "<p><strong>Contact Number:</strong> $contactNumber</p>";
    echo "<p><strong>Email:</strong> $contactEmail</p>";
} else {
    echo "<p>Contact not found.</p>";
}
?>

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

    p {
        color: #333;
        margin-bottom: 10px;
    }
</style>
