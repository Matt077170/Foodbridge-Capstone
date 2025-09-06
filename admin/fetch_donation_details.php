<?php
// FILE: fetch_donation_details.php

session_start();
include '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin' || !isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access or missing ID.']);
    exit();
}

$donationId = intval($_GET['id']);
$response = ['success' => true];

try {
    // 1. Fetch main donation details, including the delivery_mode
    $stmt = $conn->prepare("SELECT delivery_mode FROM donations WHERE id = ?");
    $stmt->bind_param("i", $donationId);
    $stmt->execute();
    $result = $stmt->get_result();
    $donation = $result->fetch_assoc();

    if ($donation) {
        // Add delivery_mode to the response
        $response['delivery_mode'] = htmlspecialchars($donation['delivery_mode']);
    } else {
        throw new Exception('Donation not found.');
    }
    $stmt->close();


    // 2. Fetch donated items (assuming you have this table)
    $stmt_items = $conn->prepare("SELECT item_type, quantity, expiration_date FROM donation_items WHERE donation_id = ?");
    $stmt_items->bind_param("i", $donationId);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();
    $response['items'] = $items_result->fetch_all(MYSQLI_ASSOC);
    $stmt_items->close();

    // 3. Fetch donation conditions (assuming you have this table)
    $stmt_cond = $conn->prepare("SELECT * FROM donation_conditions WHERE donation_id = ?");
    $stmt_cond->bind_param("i", $donationId);
    $stmt_cond->execute();
    $cond_result = $stmt_cond->get_result()->fetch_assoc();
    if ($cond_result) {
        unset($cond_result['id'], $cond_result['donation_id']); // Remove unnecessary fields
        $response['conditions'] = $cond_result;
    }
    $stmt_cond->close();

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>