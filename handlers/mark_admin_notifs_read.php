<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  http_response_code(403); exit();
}

$conn->query("UPDATE admin_notifications SET is_read = 1 WHERE is_read = 0");
