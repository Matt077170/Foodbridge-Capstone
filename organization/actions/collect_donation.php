<?php
session_start();
include '../../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'organization') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// ...
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Get the raw JSON from the request body
    $json_data = file_get_contents('php://input');

    // 2. Decode the JSON into a PHP array
    $data = json_decode($json_data, true);

    // 3. Check if the donation_id exists in the decoded data
    if (isset($data['donation_id'])) {
        $donation_id = intval($data['donation_id']);
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
         echo json_encode(['success' => false, 'message' => 'Invalid request: donation_id missing']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}