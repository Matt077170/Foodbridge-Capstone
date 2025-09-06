<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'edit') {
    $id       = intval($_POST['id']);
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $address  = trim($_POST['address']);
    $contact  = trim($_POST['contact']);
    $birthday = trim($_POST['birthday']); // NEW FIELD

    $sql = "UPDATE users 
            SET fullname=?, email=?, address=?, contact=?, birthday=? 
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $fullname, $email, $address, $contact, $birthday, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    exit;
}

if ($action === 'delete') {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM users WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
