<?php
session_start();
include '../includes/db.php';
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['donation_id'];
    $action = $_POST['action'];
    $status = $action === 'approve' ? 'approved' : 'rejected';

    $stmt = $conn->prepare("UPDATE donations SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO audit_trail (user_id, action, role) VALUES (?, ?, ?)");
    $msg = "$action donation $id";
    $role = $_SESSION['user']['role'];
    $stmt->bind_param("iss", $_SESSION['user']['id'], $msg, $role);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM donations WHERE status = 'pending'");
?>
<h2>Approve Donations</h2>
<?php while ($row = $result->fetch_assoc()): ?>
    <form method="post">
        Donation #<?= $row['id'] ?> - <?= htmlspecialchars($row['caption']) ?>
        <input type="hidden" name="donation_id" value="<?= $row['id'] ?>">
        <button name="action" value="approve">Approve</button>
        <button name="action" value="reject">Reject</button>
    </form>
<?php endwhile; ?>