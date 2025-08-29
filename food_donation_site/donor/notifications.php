<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'donor') {
    header("Location: ../login.php");
    exit();
}

$donor_id = $_SESSION['user']['id'];
$noti = $conn->prepare("SELECT message, created_at FROM notifications WHERE donor_id = ? ORDER BY created_at DESC");
$noti->bind_param("i", $donor_id);
$noti->execute();
$resNoti = $noti->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { padding: 30px; }
    </style>
</head>
<body>
    <h2>Notifications</h2>
    <a href="dashboard.php" class="btn btn-primary btn-sm mb-3">Back to Dashboard</a>

    <?php if ($resNoti->num_rows > 0): ?>
        <ul class="list-group">
            <?php while ($n = $resNoti->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($n['message']) ?>
                    <span class="badge bg-secondary"><?= date("M d, Y H:i", strtotime($n['created_at'])) ?></span>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">You have no notifications.</p>
    <?php endif; ?>
</body>
</html>
