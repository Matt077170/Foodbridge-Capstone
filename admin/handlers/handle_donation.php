<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

// Require admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit(); // Added exit()
}

if (!isset($_POST['id']) || !isset($_POST['action'])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit(); // Added exit()
}

$donation_id = intval($_POST['id']);
$action = $_POST['action'];

$statusMap = [
    "approve" => "approved",
    "reject"  => "rejected",
    "undo"    => "pending"
];

if (!array_key_exists($action, $statusMap)) {
    echo json_encode(["success" => false, "message" => "Invalid action"]);
    exit(); // Added exit()
}

$newStatus = $statusMap[$action];

// update donation
$stmt = $conn->prepare("UPDATE donations SET status=? WHERE id=?");
$stmt->bind_param("si", $newStatus, $donation_id);
if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "DB update failed"]);
    exit(); // Added exit()
}

// log it
$log = $conn->prepare("INSERT INTO donation_logs (donation_id, action, timestamp) VALUES (?, ?, NOW())");
$log->bind_param("is", $donation_id, $action);
$log->execute();

echo json_encode(["success" => true, "message" => "Donation $action successfully", "newStatus" => $newStatus]);
exit(); // Added exit()
?>
