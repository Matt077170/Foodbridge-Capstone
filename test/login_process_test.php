<?php
$valid_users = [
    'donor@example.com' => ['password' => 'donor123', 'role' => 'donor'],

];

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if (isset($valid_users[$email]) && $valid_users[$email]['password'] === $password && $valid_users[$email]['role'] === $role) {
    $_SESSION['user'] = ['email' => $email, 'role' => $role];
    header("Location: dashboard.php");
    
} else {
    echo "Invalid login";
}
?>