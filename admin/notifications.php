<?php
session_start();
include '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode([]);
    exit();
}

$res = $conn->query("SELECT id, message, created_at, is_read 
                     FROM admin_notifications 
                     ORDER BY created_at DESC LIMIT 10");

$notifs = [];
while ($row = $res->fetch_assoc()) {
    $notifs[] = [
        "id" => $row['id'],
        "message" => $row['message'],
        "when" => date("M j, g:i a", strtotime($row['created_at'])),
        "is_read" => (bool)$row['is_read']
    ];
}

echo json_encode($notifs);
