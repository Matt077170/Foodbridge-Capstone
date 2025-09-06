<?php
session_start();
include '../includes/db.php';

// Check for admin role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../logout.php");
    exit();
}

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $admin_user_id = $_SESSION['user']['id'];
    $admin_role = $_SESSION['user']['role'];

    // Input validation
    if (empty($fullname) || empty($email) || empty($password) || empty($role)) {
        $message = "All fields are required.";
        $message_type = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $message_type = "danger";
    } else {
        // Check for existing user with the same username or email
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $message = "Username or email already exists.";
            $message_type = "danger";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $status = 'approved'; // Admins create approved accounts

            $stmt_insert = $conn->prepare("INSERT INTO users (fullname, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("sssss", $fullname, $email, $hashed_password, $role, $status);
            
            if ($stmt_insert->execute()) {
                // Log the action after a successful update
                $log_description = "Created a new user account for: " . $fullname . " with role: " . $role;
                log_action($conn, $admin_user_id, $log_description, $admin_role);

                $message = "Account for " . htmlspecialchars($fullname) . " created successfully!";
                $message_type = "success";
            } else {
                $message = "Error creating account: " . $conn->error;
                $message_type = "danger";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="addaccount.css">
</head>
<div class="container">
        <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>
        <h2 class="mb-4 animate__animated animate__fadeInDown">Add New Account</h2>

        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="add_account.php">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="donor">Donor</option>
                            <option value="organization">Organization</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
