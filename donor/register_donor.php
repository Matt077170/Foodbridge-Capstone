<?php
session_start(); // Add session_start() at the very top
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // File uploads
    $valid_id = null;
    $selfie = null;

    if (!empty($_FILES['valid_id']['name'])) {
        $valid_id = time() . "_" . basename($_FILES['valid_id']['name']);
        move_uploaded_file($_FILES['valid_id']['tmp_name'], "../uploads/ids/" . $valid_id);
    }

    if (!empty($_FILES['selfie']['name'])) {
        $selfie = time() . "_" . basename($_FILES['selfie']['name']);
        move_uploaded_file($_FILES['selfie']['tmp_name'], "../uploads/selfies/" . $selfie);
    }

    // --- Check for duplicate data before inserting ---
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR fullname = ? OR contact = ?");
    $check_stmt->bind_param("sss", $email, $fullname, $contact);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        $_SESSION['error_message'] = "Registration failed. A user with that email, full name, or contact number already exists.";
        header("Location: register_donor.php");
        exit();
    }
    // --- End of duplicate check ---

    $stmt = $conn->prepare("INSERT INTO users (fullname, email, birthday, contact, address, password, role, status, valid_id, selfie) VALUES (?, ?, ?, ?, ?, ?, 'donor', 'pending', ?, ?)");
    $stmt->bind_param("ssssssss", $fullname, $email, $birthday, $contact, $address, $password, $valid_id, $selfie);
    
    // Use an if/else to check for successful execution
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Registration successful! Please wait for admin approval.";


    } else {
        $_SESSION['error_message'] = "Something went wrong during registration. Please try again.";
    }

    $stmt->close();
    header("Location: register_donor.php");
    exit();
}

// --- Display messages from session variables ---
$message = null;
$message_type = null;

if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    $message_type = 'success';
    unset($_SESSION['success_message']);
} elseif (isset($_SESSION['error_message'])) {
    $message = $_SESSION['error_message'];
    $message_type = 'danger';
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function validateForm(e) {
            const pass = document.getElementById("password").value;
            const confirm = document.getElementById("confirm_password").value;
            if (pass !== confirm) {
                e.preventDefault();
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="row g-0">
                    <!-- Left Form -->
                    <div class="col-md-7 p-4">
                        <h3 class="mb-4 text-primary">Donor Registration</h3>
                        
                        <!-- Display success/error messages here -->
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm(event)">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="fullname" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Birthday</label>
                                <input type="date" name="birthday" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Upload Valid ID</label>
                                <input type="file" name="valid_id" class="form-control" accept="image/*" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Upload Selfie with ID</label>
                                <input type="file" name="selfie" class="form-control" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                            <a href="../login.php" class="btn btn-link w-100 mt-2">Back to Login</a>
                        </form>
                    </div>
                    <!-- Right Info -->
                    <div class="col-md-5 bg-primary text-white d-flex flex-column justify-content-center p-4">
                        <h4 class="mb-3">Why Register as a Donor?</h4>
                        <p>Help communities by donating food directly to organizations. Your ID and Selfie ensure authenticity.</p>
                        <ul>
                            <li>Verified identity</li>
                            <li>Secure donation system</li>
                            <li>Track your contributions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
