<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'donor') {
    header("Location: ../login.php");
    exit();
}

$id = $_SESSION['user']['id'];
$msg = "";

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET fullname=?, address=?, contact=?, email=? WHERE id=?");
    $stmt->bind_param("ssssi", $fullname, $address, $contact, $email, $id);
    $stmt->execute();
    $msg = "Profile updated.";
}

// Update password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $result = $conn->prepare("SELECT password FROM users WHERE id=?");
    $result->bind_param("i", $id);
    $result->execute();
    $hashed = $result->get_result()->fetch_assoc()['password'];

    if (!password_verify($current, $hashed)) {
        $msg = "<span style='color:red'>Incorrect current password.</span>";
    } elseif ($new !== $confirm) {
        $msg = "<span style='color:red'>Passwords do not match.</span>";
    } else {
        $new_hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $new_hashed, $id);
        $stmt->execute();
        $msg = "Password changed successfully.";
    }
}

// Fetch current info
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donor Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>My Profile</h2>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <form method="post" class="mb-4">
        <input type="hidden" name="update_profile" value="1">
        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Address</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Contact Number</label>
            <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($user['contact']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>

    <h4>Change Password</h4>
    <form method="post">
        <input type="hidden" name="change_password" value="1">
        <div class="mb-3">
            <label>Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-warning">Change Password</button>
    </form>

    <br>
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</body>
</html>
