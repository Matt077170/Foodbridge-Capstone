<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];
switch ($user['role']) {
    case 'admin': header("Location: admin/dashboard.php"); break;
    case 'donor': header("Location: donor/dashboard.php"); break;
    case 'organization': header("Location: organization/dashboard.php"); break;
}
?>