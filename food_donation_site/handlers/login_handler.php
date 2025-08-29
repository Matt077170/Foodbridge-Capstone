<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'approved' LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;

            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    header("Location: ../admin/dashboard.php");
                    break;
                case 'donor':
                    header("Location: ../donor/dashboard.php");
                    break;
                case 'organization':
                    header("Location: ../organization/dashboard.php");
                    break;
                default:
                    // Unknown role fallback
                    header("Location: ../login.php?error=unknown_role");
                    break;
            }
            exit();
        } else {
            header("Location: ../login.php?error=invalid_password");
            exit();
        }
    } else {
        header("Location: ../login.php?error=user_not_found");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
