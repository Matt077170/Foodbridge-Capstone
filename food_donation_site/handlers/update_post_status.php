<?php
session_start();
header('Content-Type: application/json');
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$post_id = intval($_POST['post_id'] ?? 0);
$status = $_POST['status'] ?? '';

$valid_statuses = ['approve' => 'approved', 'reject' => 'rejected', 'pending' => 'pending'];

if (!$post_id || !isset($valid_statuses[$status])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit();
}

$final_status = $valid_statuses[$status];

$stmt = $conn->prepare("UPDATE posts SET status = ? WHERE id = ?");
$stmt->bind_param("si", $final_status, $post_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database update failed']);
}
