<?php
// Start session if needed (for future login/logout integration)
 session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FoodBridge - Bridging Communities</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="img/foodbridge_logo.png" alt="FoodBridge Logo" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a href="#about" class="nav-link">ABOUT US</a></li>
                <li class="nav-item"><a href="#how-it-works" class="nav-link">HOW IT WORKS</a></li>
                <li class="nav-item"><a href="#food-waste" class="nav-link">ABOUT FOOD WASTE</a></li>
                <li class="nav-item">
                    <a href="login.php" class="btn btn-warning ms-3">Log-in</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero animate__animated animate__fadeIn">
    <div class="container">
        <h1 class="display-5 fw-bold">Bridging Communities Through Food and Generosity</h1>
        <p class="lead mt-3">
            At FoodBridge, we connect local donors, vendors, and individuals with soup kitchens to reduce food waste and fight hunger.
            By redistributing surplus food, we ensure that nutritious meals reach those who need them most.
        </p>

        <h2 class="mt-5 fw-bold">Sign Up First</h2>
        <div class="cta-buttons mt-4">
            <a href="signup_donor.php" class="btn btn-lg btn-outline-danger">I Want to Donate</a>
            <a href="signup_org.php" class="btn btn-lg btn-outline-warning">I Need Donations</a>
        </div>
    </div>
</section>

<!-- What We Do -->
<section class="what-we-do" id="about">
    <div class="container">
        <h3 class="mb-4 text-danger">What We Do</h3>
        <p class="mb-5 text-muted">Our efforts in local communities create a ripple effect, connecting donors, vendors, and soup kitchens to reduce food waste and fight hunger.</p>

        <div class="row">
            <div class="col-md-3 mb-4 animate__animated animate__fadeInUp">
                <div class="icon text-warning">🤝</div>
                <h5>Connect Communities</h5>
            </div>
            <div class="col-md-3 mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="icon text-danger">🍽️</div>
                <h5>Nourish the Hungry</h5>
            </div>
            <div class="col-md-3 mb-4 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="icon text-success">🌱</div>
                <h5>Promote Sustainability</h5>
            </div>
            <div class="col-md-3 mb-4 animate__animated animate__fadeInUp animate__delay-3s">
                <div class="icon text-info">💡</div>
                <h5>Inspire Change</h5>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="site-footer">
    <div class="container footer-content">
        <div class="footer-left">
            <img src="img/foodbridge_logo.png" alt="FoodBridge Logo" class="footer-logo">
            <p class="copyright">&copy; 2025 FoodBridge. All rights reserved.</p>
        </div>
        <div class="footer-center">
            <ul>
                <li><a href="#">About Us</a></li>
                <li><a href="#">How It Works</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
            </ul>
        </div>
        <div class="footer-right">
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
           
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
