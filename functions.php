<?php
// Function to get username by user ID
function getUsername($db, $user_id)
{
    $query = "SELECT username FROM users WHERE id = $user_id";
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        return $result->fetch_assoc()["username"];
    } else {
        return "Unknown User";
    }
}
?>
