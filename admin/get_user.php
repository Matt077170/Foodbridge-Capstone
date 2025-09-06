<?php
include '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(null);
    exit;
}

$id = intval($_GET['id']);

// FIX: Added 'address' to the SELECT statement
$sql = "SELECT id, fullname, email, address, contact, birthday, valid_id, selfie 
        FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    echo json_encode($user);
} else {
    echo json_encode(null);
}

$stmt->close();
$conn->close();
