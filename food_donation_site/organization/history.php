<?php
session_start();
include '../includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'organization') {
    header("Location: ../login.php");
    exit();
}
$org_id = $_SESSION['user']['id'];
$query = "SELECT donations.*, users.fullname AS donor_name FROM donations JOIN users ON donations.donor_id = users.id WHERE donations.organization_id = ? ORDER BY donations.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $org_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head><title>Received Donations History</title></head>
<body>
<h2>Received Donation History</h2>
<table border="1">
<tr><th>Date</th><th>Donor Name</th><th>Caption</th><th>Delivery Mode</th><th>Status</th></tr>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['created_at'] ?></td>
    <td><?= htmlspecialchars($row['donor_name']) ?></td>
    <td><?= htmlspecialchars($row['caption']) ?></td>
    <td><?= $row['delivery_mode'] ?><?= $row['same_day_delivery'] ? " (Same Day)" : "" ?></td>
    <td><?= ucfirst($row['status']) ?></td>
</tr>
<?php endwhile; ?>
</table>
<a href="dashboard.php">Back to Dashboard</a>
</body>
</html>