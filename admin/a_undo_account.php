<?php
session_start();
include '../includes/db.php';


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../logout.php");
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action_id'])) {
    $action_id = $_POST['action_id'];

    $stmt_get_log = $conn->prepare("SELECT action_description FROM audit_trail WHERE id = ?");
    $stmt_get_log->bind_param("i", $action_id);
    $stmt_get_log->execute();
    $result = $stmt_get_log->get_result();
    $log_data = $result->fetch_assoc();
    $stmt_get_log->close();

    if ($log_data) {
        $description = $log_data['action_description'];
        $undo_success = false;

        if (strpos($description, 'Deleted user account for:') !== false) {
            // This is a placeholder for a true undo. Realistically, you would need to restore data from a backup.
            // For this implementation, we will log that an undo was attempted.
            log_action($conn, $_SESSION['user']['id'], "Attempted to undo deletion of account: " . $description, $_SESSION['user']['role']);
            $message = "Attempt to undo account deletion. Note: Actual data restoration requires a backup. This action has been logged.";
            $message_type = "warning";
            $undo_success = true;
        } else if (strpos($description, 'Created a new user account for:') !== false) {
            // Undo a user creation
            $username = explode(':', $description)[1];
            $stmt_delete = $conn->prepare("DELETE FROM users WHERE fullname = ?");
            $stmt_delete->bind_param("s", $username);
            if ($stmt_delete->execute()) {
                log_action($conn, $_SESSION['user']['id'], "Undid user creation for: " . trim($username), $_SESSION['user']['role']);
                $message = "Undo successful: User " . htmlspecialchars(trim($username)) . " has been deleted.";
                $message_type = "success";
                $undo_success = true;
            } else {
                $message = "Failed to undo user creation. Error: " . $conn->error;
                $message_type = "danger";
            }
        } else {
            $message = "This action cannot be undone automatically.";
            $message_type = "danger";
        }

    } else {
        $message = "Action not found in audit trail.";
        $message_type = "danger";
    }

}

// Fetch recent audit trail entries for display
$sql = "SELECT * FROM audit_trail ORDER BY timestamp DESC LIMIT 20";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Undo History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
    <h2 class="mb-4">Undo History</h2>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <p class="text-muted">You can undo certain recent administrative actions from this page.</p>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Action Description</th>
                            <th>User Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['timestamp']) ?></td>
                                    <td><?= htmlspecialchars($row['action_description']) ?></td>
                                    <td><?= htmlspecialchars($row['user_role']) ?></td>
                                    <td>
                                        <form method="POST" action="undo_history.php" class="d-inline">
                                            <input type="hidden" name="action_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Are you sure you want to undo this action?');">Undo</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No recent actions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
