<?php
// FILENAME manage_admin.php
session_start();
include '../includes/db.php';


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../logout.php");
    exit();
}

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'manage';

$message = '';
$message_type = '';

// Handle Delete Admin Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $user_id = $_POST['delete_id'];
    
    // Prevent an admin from deleting themselves
    if ($user_id == $_SESSION['user']['id']) {
        $_SESSION['message'] = "You cannot delete your own account.";
        $_SESSION['message_type'] = "danger";
    } else {
        $stmt_get_user = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmt_get_user->bind_param("i", $user_id);
        $stmt_get_user->execute();
        $result_user = $stmt_get_user->get_result();
        $user_data = $result_user->fetch_assoc();
        $stmt_get_user->close();

        if ($user_data) {
            $stmt_delete = $conn->prepare("UPDATE users SET role = 'user' WHERE id = ?");
            $stmt_delete->bind_param("i", $user_id);

            if ($stmt_delete->execute()) {
                log_action($conn, $_SESSION['user']['id'], "Deleted admin account for: " . $user_data['email'], $_SESSION['user']['role']);
                $_SESSION['message'] = "Admin account for " . htmlspecialchars($user_data['email']) . " has been successfully deleted. You can undo this action from the History tab.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Failed to delete admin account. Error: " . $conn->error;
                $_SESSION['message_type'] = "danger";
            }
            $stmt_delete->close();
        } else {
            $_SESSION['message'] = "User not found.";
            $_SESSION['message_type'] = "danger";
        }
    }
    header("Location: manage_admin.php");
    exit();
}


// Handle Undo Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action_id'])) {
    $action_id = $_POST['action_id'];

    $stmt_get_log = $conn->prepare("SELECT action FROM audit_trail WHERE id = ?");
    $stmt_get_log->bind_param("i", $action_id);
    $stmt_get_log->execute();
    $result = $stmt_get_log->get_result();
    $log_data = $result->fetch_assoc();
    $stmt_get_log->close();

    if ($log_data) {
        $action = $log_data['action'];
        
        // Undo the deletion by changing the user's role back to 'admin'
        if (strpos($action, 'Deleted admin account for:') !== false) {
            $deleted_email = trim(explode(':', $action)[1]);
            $stmt_undo = $conn->prepare("UPDATE users SET role = 'admin' WHERE email = ?");
            $stmt_undo->bind_param("s", $deleted_email);
            
            if ($stmt_undo->execute()) {
                log_action($conn, $_SESSION['user']['id'], "Undid deletion for: " . $deleted_email, $_SESSION['user']['role']);
                $message = "Undo successful: The admin role for " . htmlspecialchars($deleted_email) . " has been restored.";
                $message_type = "success";
            } else {
                $message = "Failed to undo deletion. Error: " . $conn->error;
                $message_type = "danger";
            }
            $stmt_undo->close();
        } else {
            $message = "This action cannot be undone automatically.";
            $message_type = "danger";
        }
    } else {
        $message = "Action not found in audit trail.";
        $message_type = "danger";
    }
    // Redirect back to the history tab with the message
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $message_type;
    header("Location: manage_admin.php?tab=history");
    exit();
}

// Check for a redirected message from other pages
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Fetch all admin users
$sql_admins = "SELECT id, fullname, contact, email FROM users WHERE role = 'admin'";
$result_admins = $conn->query($sql_admins);

// Fetch recently deleted admins from the audit trail
$sql_history = "SELECT id, timestamp, action FROM audit_trail WHERE action LIKE 'Deleted admin account for:%' ORDER BY timestamp DESC LIMIT 20";
$result_history = $conn->query($sql_history);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Admins & History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="container mt-5">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
    <h2 class="mb-4">Manage Admins & History</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $active_tab == 'manage' ? 'active' : '' ?>" href="?tab=manage">Manage Admins</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab == 'history' ? 'active' : '' ?>" href="?tab=history">History</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade <?= $active_tab == 'manage' ? 'show active' : '' ?>" id="manage-admins-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_admins->num_rows > 0): ?>
                                    <?php while($row = $result_admins->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                                            <td><?= htmlspecialchars($row['contact']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td>
                                                <a href="a_edit_account.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary me-2"><i class="fas fa-edit"></i> Edit</a>
                                                <form method="POST" action="manage_admin.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this admin account? This action can be undone.');">
                                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-user-minus"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No admin accounts found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade <?= $active_tab == 'history' ? 'show active' : '' ?>" id="history-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="text-muted">You can use this page to undo recently deleted admin accounts.</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Deleted Admin Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_history->num_rows > 0): ?>
                                    <?php while($row = $result_history->fetch_assoc()): 
                                        preg_match('/Deleted admin account for: (.*)/', $row['action'], $matches);
                                        $deleted_email = isset($matches[1]) ? $matches[1] : 'N/A';
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['timestamp']) ?></td>
                                            <td><?= htmlspecialchars($deleted_email) ?></td>
                                            <td>
                                                <form method="POST" action="manage_admin.php" class="d-inline" onsubmit="return confirm('Are you sure you want to undo this deletion and restore this account?');">
                                                    <input type="hidden" name="action_id" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-success">Undo</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No deleted admin accounts found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
