<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'organization') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donation_id'])) {
    $donation_id = intval($_POST['donation_id']);
    $org_id = $_SESSION['user']['id'];

    // Ensure this donation belongs to this org and is approved
    $check = $conn->prepare("SELECT id FROM donations WHERE id = ? AND organization_id = ? AND status = 'approved'");
    $check->bind_param("ii", $donation_id, $org_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $update = $conn->prepare("UPDATE donations SET status = 'collected', updated_at = NOW() WHERE id = ?");
        $update->bind_param("i", $donation_id);
        if ($update->execute()) {
            echo json_encode(['success' => true, 'message' => 'Donation collected successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
        $update->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid donation or already collected']);
    }
    $check->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
