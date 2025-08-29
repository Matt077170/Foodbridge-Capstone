<?php
session_start();
include '../includes/db.php';
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['user_id'];
    $action = $_POST['action'];
    $status = $action === 'approve' ? 'approved' : 'rejected';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO audit_trail (user_id, action, role) VALUES (?, ?, ?)");
    $msg = "$action user account $id";
    $role = $_SESSION['user']['role'];
    $stmt->bind_param("iss", $_SESSION['user']['id'], $msg, $role);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM users WHERE status = 'pending'");
?>
<h2>Approve New Accounts</h2>
<?php while ($row = $result->fetch_assoc()): ?>
    <form method="post">
        <?= htmlspecialchars($row['fullname']) ?> (<?= $row['email'] ?>)
        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
        <button name="action" value="approve">Approve</button>
        <button name="action" value="reject">Reject</button>
    </form>
<?php endwhile; ?>