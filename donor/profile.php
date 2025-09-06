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

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/profile_pictures/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = $_FILES['profile_picture']['name'];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = $id . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . basename($new_file_name);
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        // Validate file type
        if (!in_array($image_file_type, $allowed_types)) {
            $msg = "<span style='color:red'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</span>";
        } elseif ($_FILES['profile_picture']['size'] > 5000000) { // 5MB limit
            $msg = "<span style='color:red'>Sorry, your file is too large.</span>";
        } else {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                // Update the profile_picture column in the database
                $stmt = $conn->prepare("UPDATE users SET profile_picture=? WHERE id=?");
                $stmt->bind_param("si", $target_file, $id);
                $stmt->execute();
                $msg = "Profile picture uploaded successfully.";
            } else {
                $msg = "<span style='color:red'>Sorry, there was an error uploading your file.</span>";
            }
        }
    } else {
        $msg = "<span style='color:red'>No file selected or an upload error occurred.</span>";
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

    <!-- Display Profile Picture and Upload Form -->
    <div class="card mb-4">
        <div class="card-body text-center">
            <h5 class="card-title">Profile Picture</h5>
            <img src="<?= htmlspecialchars($user['profile_picture'] ?? 'uploads/profile_pictures/default-avatar.png') ?>" alt="Profile Picture" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="upload_picture" value="1">
                <div class="input-group mb-3">
                    <input type="file" class="form-control" name="profile_picture" required>
                    <button class="btn btn-secondary" type="submit">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Profile Form -->
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

    <hr>

    <!-- Change Password Form -->
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
