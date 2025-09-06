<?php
session_start();
include '../includes/db.php';


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../logout.php");
    exit();
}

$message = '';
$message_type = '';

$user_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id_post = $_POST['user_id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Get old data for logging purposes
    $stmt_old = $conn->prepare("SELECT fullname, email, role FROM users WHERE id = ?");
    $stmt_old->bind_param("i", $user_id_post);
    $stmt_old->execute();
    $result_old = $stmt_old->get_result();
    $old_data = $result_old->fetch_assoc();
    $stmt_old->close();

    $sql = "UPDATE users SET fullname = ?, email = ?, role = ? WHERE id = ?";
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET fullname = ?, email = ?, password = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $fullname, $email, $hashed_password, $role, $user_id_post);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $fullname, $email, $role, $user_id_post);
    }
    
    if ($stmt->execute()) {
        $log_message = "Edited account for user ID: " . $user_id_post . ". Changes: ";
        $changes = [];
        if ($old_data['fullname'] != $fullname) $changes[] = "Full Name changed from '{$old_data['fullname']}' to '{$fullname}'";
        if ($old_data['email'] != $email) $changes[] = "Email changed from '{$old_data['email']}' to '{$email}'";
        if ($old_data['role'] != $role) $changes[] = "Role changed from '{$old_data['role']}' to '{$role}'";
        if (!empty($password)) $changes[] = "Password was reset";

        if (!empty($changes)) {
            $log_message .= implode(", ", $changes) . ".";
            log_action($conn, $_SESSION['user']['id'], $log_message, $_SESSION['user']['role']);
        }

        $message = "User account updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating user account: " . $conn->error;
        $message_type = "danger";
    }
    $stmt->close();

} else if ($user_id) {
    $stmt = $conn->prepare("SELECT id, fullname, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $stmt->close();

    if (!$user_data) {
        $message = "User not found.";
        $message_type = "danger";
        $user_id = null;
    }
} else {
    $message = "No user ID provided.";
    $message_type = "danger";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
    <h2 class="mb-4">Edit Account</h2>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($user_id && $user_data): ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="edit_account.php">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_data['id']) ?>">
                <div class="mb-3">
                    <label for="fullname" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($user_data['fullname']) ?>" required>
                </div>
             
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="donor" <?= $user_data['role'] == 'donor' ? 'selected' : '' ?>>Donor</option>
                        <option value="organization" <?= $user_data['role'] == 'organization' ? 'selected' : '' ?>>Organization</option>
                        <option value="admin" <?= $user_data['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
