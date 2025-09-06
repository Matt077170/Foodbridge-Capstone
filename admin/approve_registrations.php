<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}

if (isset($_GET['id'], $_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE users SET status='active' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $actMsg = "Approved account ID $id";
    } elseif ($action === 'deny') {
        $stmt = $conn->prepare("UPDATE users SET status='denied' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $actMsg = "Denied account ID $id";
    }

    // Log to audit trail
    $admin_id = $_SESSION['user']['id'];
    $role = "admin";
    $stmt = $conn->prepare("INSERT INTO audit_trail (user_id, action, role, timestamp) VALUES (?,?,?,NOW())");
    $stmt->bind_param("iss", $admin_id, $actMsg, $role);
    $stmt->execute();

    header("Location: pending_regs.php?msg=Action+completed");
    exit();
}
header("Location: pending_regs.php?msg=Invalid+action");
