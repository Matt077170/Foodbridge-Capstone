<?php
session_start();
include '../includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  http_response_code(403); exit();
}

$res = $conn->query("SELECT id, message, created_at, is_read FROM admin_notifications ORDER BY created_at DESC LIMIT 5");
$out = [];
while ($n = $res->fetch_assoc()) {
  $out[] = [
    'id' => $n['id'],
    'message' => $n['message'],
    'when' => date('M j, g:i a', strtotime($n['created_at'])),
    'is_read' => (bool)$n['is_read']
  ];
}
echo json_encode($out);
