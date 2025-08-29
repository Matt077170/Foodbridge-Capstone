<?php
session_start();
include '../includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'donor') {
    header("Location: ../login.php");
    exit();
}
$donor_id = $_SESSION['user']['id'];
$keyword = isset($_GET['keyword']) ? '%' . $_GET['keyword'] . '%' : '%';
$delivery = isset($_GET['delivery_mode']) && $_GET['delivery_mode'] !== '' ? $_GET['delivery_mode'] : '%';

$query = "SELECT * FROM donations WHERE donor_id = ? AND caption LIKE ? AND delivery_mode LIKE ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $donor_id, $keyword, $delivery);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head><title>Donation History</title></head>
<body>
<h2>Your Donation History</h2>

<form method="get">
    <label>Search Caption:</label>
    <input type="text" name="keyword" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
    <label>Delivery Mode:</label>
    <select name="delivery_mode">
        <option value="">All</option>
        <option value="Drop-off" <?= (($_GET['delivery_mode'] ?? '') === 'Drop-off') ? 'selected' : '' ?>>Drop-off</option>
        <option value="Pickup" <?= (($_GET['delivery_mode'] ?? '') === 'Pickup') ? 'selected' : '' ?>>Pickup</option>
        <option value="Courier" <?= (($_GET['delivery_mode'] ?? '') === 'Courier') ? 'selected' : '' ?>>Courier</option>
    </select>
    <button type="submit">Filter</button>
</form>

<table border="1">
<tr><th>Date</th><th>Caption</th><th>Delivery Mode</th><th>Status</th></tr>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['created_at'] ?></td>
    <td><?= htmlspecialchars($row['caption']) ?></td>
    <td><?= $row['delivery_mode'] ?><?= $row['same_day_delivery'] ? " (Same Day)" : "" ?></td>
    <td><?= ucfirst($row['status']) ?></td>
</tr>
<?php endwhile; ?>
</table>
<a href="dashboard.php">Back to Dashboard</a>
</body>
</html>