<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/PHPMailer/phpmailer/src/Exception.php';
require 'vendor/PHPMailer/phpmailer/src/PHPMailer.php';
require 'vendor/PHPMailer/phpmailer/src/SMTP.php';

include 'includes/db.php';

$message = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check token (ignore NOW(), we'll validate in PHP)
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token=? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $reset = $result->fetch_assoc();
        $user_id = $reset['user_id'];
        $expires_at = strtotime($reset['expires_at']);

        if ($expires_at > time()) {
            // Still valid
            $stmtUser = $conn->prepare("SELECT email FROM users WHERE id=? LIMIT 1");
            $stmtUser->bind_param("i", $user_id);
            $stmtUser->execute();
            $userResult = $stmtUser->get_result();
            $user = $userResult->fetch_assoc();
            $user_email = $user['email'];

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                // Update password
                $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
                $stmt->bind_param("si", $new_password, $user_id);
                $stmt->execute();

                // Delete used token
                $conn->query("DELETE FROM password_resets WHERE token='$token'");

                // Send confirmation email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'foodbridgeph.mail@gmail.com'; // your Gmail
                    $mail->Password = 'nahv pffm hjgk nkyb'; // Gmail App password
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port = 465;

                    $mail->setFrom('foodbridgeph.mail@gmail.com', 'Food Bridge PH');
                    $mail->addAddress($user_email);

                    $mail->isHTML(true);
                    $mail->Subject = "Password Changed Successfully";
                    $mail->Body = "
                        <h2>Password Changed</h2>
                        <p>Your password has been successfully updated.</p>
                        <p>If you did not request this change, please contact support immediately.</p>
                    ";

                    $mail->send();
                } catch (Exception $e) {
                    // Optionally log error
                }

                // Redirect to login page with success message
                header("Location: login.php?reset=done");
                exit();
            }
        } else {
            $message = "<div class='alert alert-danger'>❌ Token has expired.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>❌ Invalid token.</div>";
    }
} else {
    $message = "<div class='alert alert-warning'>⚠️ No token provided.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
        <div class="card shadow p-4" style="max-width:400px; width:100%;">
            <h3 class="text-center mb-3">Reset Password</h3>
            <?= $message ?>
            <?php if (isset($reset) && isset($expires_at) && $expires_at > time() && empty($_POST)) { ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Change Password</button>
            </form>
            <?php } ?>
            <div class="text-center mt-3">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
