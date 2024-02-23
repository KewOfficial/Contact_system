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

// Function to sanitize user inputs
function sanitize_input($input)
{
    return htmlspecialchars(stripslashes(trim($input)));
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Retrieve contact_id from both POST and GET methods
$contact_id = isset($_POST["contact_id"]) ? sanitize_input($_POST["contact_id"]) : (isset($_GET["id"]) ? sanitize_input($_GET["id"]) : null);

if ($contact_id !== null) {
    // Remove contact from the database
    $query_remove_contact = "DELETE FROM contacts WHERE id = $contact_id AND user_id = {$_SESSION['user_id']}";
    $result_remove_contact = $db->query($query_remove_contact);

    if ($result_remove_contact) {
        echo "Contact removed successfully.";
    } else {
        echo "Failed to remove contact. Please try again.";
        echo "MySQL Error: " . $db->error;
    }
} else {
    echo "Invalid request. Contact ID not provided.";
}
?>
