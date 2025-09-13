<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FoodBridge-Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="images/logo.png" alt="FoodBridge Logo" class="logo-image">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a href="aboutus.php" class="nav-link">ABOUT US</a></li>
                <li class="nav-item"><a href="howitworks.php" class="nav-link">HOW IT WORKS</a></li>
                <li class="nav-item"><a href="foodwaste.php" class="nav-link">ABOUT FOOD WASTE</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="hero-section">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="display-5 fw-bold animate__animated animate__fadeInUp">Bridging Communities Through Food and Generosity</h1>
            <p class="lead mt-3 animate__animated animate__fadeInUp animate__delay-1s">
                At FoodBridge, we connect local donors, vendors, and individuals with soup kitchens to reduce food waste and fight hunger.
                By redistributing surplus food, we ensure that nutritious meals reach those who need them most.
            </p>
        </div>
        <div class="login-page">
            <div class="login-header">
                <h1>LOGIN</h1>
            </div>
            <div class="login-content">
                <div class="login-form-container">
                    <form action="process_login.php" method="post">
                        <div class="form-group">
                            <label for="email">Username/Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password <span class="required">*</span></label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role <span class="required">*</span></label>
                            <select id="role" name="role" required>
                                <option value="donor">Donor</option>
                                <option value="organization">Organization</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="login-button">LOGIN</button>
                    </form>
                    <a href="forgot_password.php" class="forgot-password-link">Forgot password?</a>
                    <p class="register-text">Don't have an account? <a href="register.php" class="register-link">Register</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>