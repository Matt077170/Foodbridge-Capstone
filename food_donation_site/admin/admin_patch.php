<?php
include '../includes/db.php';

$email = 'admin@foodsite.com';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$role = 'admin';
$status = 'approved';

// Check if admin exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND role = ?");
$stmt->bind_param("ss", $email, $role);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt_insert = $conn->prepare("INSERT INTO users (fullname, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
    $fullname = 'Admin User';
    $stmt_insert->bind_param("sssss", $fullname, $email, $password, $role, $status);
    $stmt_insert->execute();
    echo "✅ Admin account created successfully.<br>Email: admin@foodsite.com<br>Password: admin123";
} else {
    echo "ℹ️ Admin account already exists.";
}
?>
