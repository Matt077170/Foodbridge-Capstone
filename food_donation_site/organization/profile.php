<?php
session_start();
include '../includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'organization') {
    header("Location: ../login.php");
    exit();
}
$id = $_SESSION['user']['id'];
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['fullname'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET fullname=?, address=?, contact=?, email=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $address, $contact, $email, $id);
    $stmt->execute();
    $msg = "Profile updated.";
}

// Fetch profile
$org = $conn->prepare("SELECT * FROM users WHERE id=?");
$org->bind_param("i", $id);
$org->execute();
$data = $org->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Organization Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>My Profile</h2>
    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-2">
            <label>Organization Name</label>
            <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($data['fullname']) ?>">
        </div>
        <div class="mb-2">
            <label>Address</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($data['address']) ?>">
        </div>
        <div class="mb-2">
            <label>Contact Number</label>
            <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($data['contact']) ?>">
        </div>
        <div class="mb-2">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>">
        </div>
        <button type="submit" class="btn btn-success">Save Changes</button>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</body>
</html>
