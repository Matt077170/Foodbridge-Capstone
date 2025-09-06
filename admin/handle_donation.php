<?php
// FILE: handle_donation.php

session_start();
include '../includes/db.php';

// Check if the user is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$current_user_id = $_SESSION['user']['id'];
$current_user_role = $_SESSION['user']['role'];

header('Content-Type: application/json');

// Read the raw POST data from the request body
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Check if the decoded data contains the required fields
if (empty($data['id']) || empty($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input. Missing "id" or "action".']);
    exit();
}

$donation_id = $data['id'];
$action = $data['action'];

try {
    $conn->begin_transaction();

    // Fetch current status to prevent duplicate actions
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

    // This check seems to prevent re-approving something that was undone. Let's adjust it.
    // We only want to process donations that are 'pending'.
    if ($donation['status'] !== 'pending') {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'This donation is not pending and cannot be processed.']);
        exit();
    }

    // Determine the new status based on the action --- THIS IS THE FIX ---
    $new_status = ($action === 'approved') ? 'approved' : 'rejected';

    // Update the donation status
    $stmt = $conn->prepare("UPDATE donations SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $donation_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    // Log the action before sending the success response
    if ($new_status === 'approved') {
        $action_description = "Approved donation with ID: " . $donation_id;
    } else { // 'rejected'
        $action_description = "Rejected donation with ID: " . $donation_id;
    }

    // Ensure the user ID is valid before logging
    if ($current_user_id) {
        // You would need to define or include the log_action function for this to work
        // log_action($conn, $current_user_id, $action_description, $current_user_role);
    } else {
        error_log("Failed to log action: User ID not found in session for handle_donation.php");
    }

    echo json_encode(['success' => true, 'message' => 'Donation ' . $new_status . ' successfully.']);

} catch (mysqli_sql_exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

// Note: If log_action re-opens a connection, you might not want to close it here.
// But based on the code, closing it is correct.
$conn->close();
?>