<?php
session_start();
include 'includes/db.php';

$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ? AND status = 'approved'");
$stmt->bind_param("ss", $email, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    echo "User found. Checking password...<br>";
    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        echo "Login success. Redirecting...";
        header("Location: dashboard.php");
        exit();
    } else {
        echo "❌ Password does not match.";
    }
} else {
    echo "❌ No approved user found with that email/role.";
}
?>
