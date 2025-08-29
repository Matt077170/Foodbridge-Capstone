<?php
session_start();
include '../includes/db.php';
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
$result = $conn->query("SELECT audit_trail.*, users.fullname FROM audit_trail JOIN users ON audit_trail.user_id = users.id ORDER BY timestamp DESC");
?>
<h2>Audit Trail</h2>
<table border="1">
    <tr><th>User</th><th>Action</th><th>Role</th><th>Time</th></tr>
    <?php while ($log = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($log['fullname']) ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= htmlspecialchars($log['role']) ?></td>
            <td><?= $log['timestamp'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>