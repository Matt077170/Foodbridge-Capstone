<?php
// FILE: ../includes/db.php

// Your existing database connection code here...
// This is an example, your actual code may vary
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'food_donation_db';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * Logs an administrative action to a database table.
 *
 * @param mysqli $conn The database connection object.
 * @param int $userId The ID of the user performing the action.
 * @param string $actionDescription A descriptive string of the action performed.
 * @param string $userRole The role of the user (e.g., 'admin').
 */
function log_action($conn, $userId, $actionDescription, $userRole) {
    if (!$conn) {
        error_log("Failed to log action: Database connection is not available.");
        return;
    }

    // Use a prepared statement to securely insert the data.
    // The query now expects three values for 'user_id', 'action', and 'role'.
    // The 'timestamp' will be handled by a database function like CURRENT_TIMESTAMP.
    $stmt = $conn->prepare("INSERT INTO audit_trail (user_id, action, role, timestamp) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
    if ($stmt === false) {
        error_log("Failed to prepare log statement: " . $conn->error);
        return;
    }

    // The "iss" type string matches the three parameters:
    // 'i' for integer ($userId) and 'ss' for two strings ($actionDescription, $userRole).
    $stmt->bind_param("iss", $userId, $actionDescription, $userRole);
    
    if ($stmt->execute()) {
        error_log("Successfully logged action for user ID: $userId");
    } else {
        error_log("Error executing log statement: " . $stmt->error);
    }
    $stmt->close();
}
