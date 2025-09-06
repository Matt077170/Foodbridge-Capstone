<?php
// FILE: fetch_donations.php

session_start();
include '../includes/db.php';

header('Content-Type: application/json');

// Check if the user is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$type = $_GET['type'] ?? 'pending';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

// Define the query and count query based on the status
if ($type === 'pending') {
    $sql = "SELECT d.id, d.caption, d.status, u.fullname AS donor, d.created_at
            FROM donations d
            JOIN users u ON d.donor_id = u.id
            WHERE d.status = 'pending'
            ORDER BY d.created_at DESC";
    $count_sql = "SELECT COUNT(*) FROM donations WHERE status = 'pending'";
} else {
    // 'history' status includes approved, rejected, and collected donations
    $sql = "SELECT d.id, d.caption, d.status, u.fullname AS donor, d.created_at
            FROM donations d
            JOIN users u ON d.donor_id = u.id
            WHERE d.status IN ('approved', 'rejected', 'collected')
            ORDER BY d.created_at DESC
            LIMIT $limit OFFSET $offset";
    $count_sql = "SELECT COUNT(*) FROM donations WHERE status IN ('approved', 'rejected', 'collected')";
}

$donations = [];
$total_donations = 0;
$message = '';
$success = false;

try {
    // Execute the main query
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['date'] = date('M j, Y g:i a', strtotime($row['created_at']));
                unset($row['created_at']); // Remove the old key
                $donations[] = $row;
            }
            $success = true;
            $message = 'Donations fetched successfully.';
        } else {
            $success = true;
            $message = 'No donations found for this status.';
        }
    } else {
        $message = 'Query failed: ' . $conn->error;
    }

    // Execute the count query for pagination
    if ($type === 'history') {
        $total_result = $conn->query($count_sql);
        if ($total_result) {
            $total_donations = $total_result->fetch_row()[0];
        }
    }
    
} catch (mysqli_sql_exception $e) {
    $message = 'Database error: ' . $e->getMessage();
}

$conn->close();

echo json_encode([
    'success' => $success,
    'message' => $message,
    'donations' => $donations,
    'total' => $total_donations,
    'page' => $page,
    'limit' => $limit
]);
?>
