<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'vendor/PHPMailer/phpmailer/src/Exception.php';
require 'vendor/PHPMailer/phpmailer/src/PHPMailer.php';
require 'vendor/PHPMailer/phpmailer/src/SMTP.php';


include 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        $token = bin2hex(random_bytes(50));
        $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?,?)");
        $stmt->bind_param("iss", $user_id, $token, $expires_at);
        $stmt->execute();

        $reset_link = "http://localhost/foodbridge/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
             $mail->Username = 'foodbridgeph.mail@gmail.com'; // Your Gmail
    $mail->Password = 'nahv pffm hjgk nkyb';  // App password (NOT your Gmail password)
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

            $mail->setFrom('foodbridgeph.mail@gmail.com', 'Food Bridge PH');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                <h2>Forgot Password</h2>
                <p>We received a request to reset your password.</p>
                <p><a href='$reset_link'>Click here to reset your password</a></p>
                <p>This link will expire in 1 hour.</p>
            ";

            $mail->send();
            $message = "<div class='alert alert-success'>✅ An email has been sent to your inbox.</div>";
			header("Location: login.php?reset=success");
			exit();
		
			
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>❌ Error sending email. Try again later.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>⚠️ No account found with that email.</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
        <div class="card shadow p-4" style="max-width:400px; width:100%;">
            <h3 class="text-center mb-3">Forgot Password</h3>
            <?= $message ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Enter your email</label>
                    <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
