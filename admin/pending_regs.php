<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}

$pending = $conn->query("SELECT id, fullname, email, address, contact, role, created_at 
                         FROM users WHERE status='pending' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Pending Registrations</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
  <h2>Pending User Registrations</h2>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Full Name</th>
        <th>Email</th>
        <th>Contact</th>
        <th>Address</th>
        <th>Role</th>
        <th>Submitted</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while($row = $pending->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['fullname']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['contact']) ?></td>
        <td><?= htmlspecialchars($row['address']) ?></td>
        <td><?= ucfirst($row['role']) ?></td>
        <td><?= date("M j, Y g:i a", strtotime($row['created_at'])) ?></td>
        <td>
          <a href="approve_registration.php?id=<?= $row['id'] ?>&action=approve" class="btn btn-success btn-sm">Approve</a>
          <a href="approve_registration.php?id=<?= $row['id'] ?>&action=deny" class="btn btn-danger btn-sm">Deny</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
</body>
</html>
