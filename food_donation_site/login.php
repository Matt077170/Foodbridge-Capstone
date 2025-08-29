<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body>
    <div class="navbar">
        <div class="container">
            <div class="logo">
                <a href="#"><img src="img/foodbridge_logo.png" alt="FoodBridge">FoodBridge</a>
            </div>
            <nav>
                <ul>
                    <li><a href="#">ABOUT US</a></li>
                    <li><a href="#">HOW IT WORKS</a></li>
                    <li><a href="#">ABOUT FOOD WASTE</a></li>
                </ul>
            </nav>
        </div>
    </div>
    <div class="hero-section">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Bridging Communities Through Food and Generosity</h1>
                <p>At FoodBridge, we connect local donors, vendors, and individuals with soup kitchens to reduce food waste and fight hunger. By redistributing surplus food, we ensure that nutritious meals reach those who need them most. Join us in making a difference—one meal at a time.</p>
            </div>
            <div class="login-card">
                <h2>LOGIN</h2>
                <form action="process_login.php" method="post">
                    <div class="form-group">
                        <label for="email">Username/Email <span style="color:red">*</span></label>
                        <input type="text" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password <span style="color:red">*</span></label>
                        <input type="password" id="password" name="password" required>
                          <select name="role" required>
        <option value="donor">Donor</option>
        <option value="organization">Organization</option>
        <option value="admin">Admin</option>
    </select><br><br>
                    </div>
                    <button type="submit" class="login-btn">LOGIN</button>
                </form>
                <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
                <a href="register.php" class="no-account">Don't have an account?</a>
            </div>
        </div>
        <div class="hero-image-overlay">
            <img src="images/large-logo.png" class="large-logo" alt="FoodBridge Logo">
        </div>
    </div>
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
</main>

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

</body>
</html>