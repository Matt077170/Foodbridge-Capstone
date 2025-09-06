<?php
// FILE: undo_donation.php

session_start();
include '../includes/db.php';

header('Content-Type: application/json');
$current_user_id = $_SESSION['user']['id'];
$current_user_role = $_SESSION['user']['role'];

// Check if the user is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// Read the raw POST data from the request body
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (empty($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input. Missing "id".']);
    exit();
}

$donation_id = $data['id'];

try {
    $conn->begin_transaction();

    // Check if donation is in a state that can be undone
    $stmt = $conn->prepare("SELECT status FROM donations WHERE id = ?");
    $stmt->bind_param("i", $donation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $donation = $result->fetch_assoc();
    $stmt->close();

    if (!$donation) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Donation not found.']);
        exit();
    }

    if ($donation['status'] === 'pending') {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Donation is already pending.']);
		
        exit();
    }

    // Update the donation status back to pending
    $new_status = 'pending';
    $stmt = $conn->prepare("UPDATE donations SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $donation_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Donation status reverted to pending.']);
			// Log the action after a successful update
            $action_description = "Undid approval for donation with ID: " . $donation_id;
            log_action($conn, $current_user_id, $action_description, $current_user_role);
} catch (mysqli_sql_exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
