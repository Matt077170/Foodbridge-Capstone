<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodBridge - Sign Up for Soup Kitchen</title>
    <link rel="stylesheet" href="css/SignUpSkStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrNj/2P9Vf4lB1c7l3s+Fv7/J8qLz9x+T+Gq0t1f2FmG8f/n2k+y+L+9qG6d+h+J+A+c+c+u+B+S+q+F+w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <header class="navbar">
        <div class="container">
            <div class="logo">
                <a href="MainPage.php">
                    <img src="logo.png" alt="FoodBridge Logo">
                    <span>FoodBridge</span>
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="#">ABOUT US</a></li>
                    <li><a href="#">HOW IT WORKS</a></li>
                    <li><a href="#">ABOUT FOOD WASTE</a></li>
                </ul>
            </nav>
            <a href="LogIn.php" class="login-button">Log In</a>
        </div>
    </header>

    <main>
        <section class="soup-kitchen-signup-section">
            <div class="container">
                <div class="intro-text">
                    <h1>Make an Impact Today</h1>
                    <p>Join FoodBridge and be part of a movement to fight food waste and feed communities.</p>
                    <p>It only takes a minute to start making a difference.</p>
                </div>

                <div class="signup-card">
                    <h2>Sign up for Soup Kitchen</h2>
                    <form>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="organization-name">ORGANIZATION NAME:</label>
                                <input type="text" id="organization-name" name="organization_name" placeholder="ORGANIZATION NAME">
                            </div>
                            <div class="form-group upload-group">
                                <label for="upload-id">UPLOAD VALID ID/PERMIT:</label>
                                <span class="subtext">pictured text encouraged!</span>
                                <input type="file" id="upload-id" name="upload_id" accept="image/*,.pdf" style="display: none;">
                                <label for="upload-id" class="upload-btn">Upload ID Image</label>
                            </div>
                            <div class="form-group">
                                <label for="email">EMAIL ADDRESS:</label>
                                <input type="email" id="email" name="email" placeholder="EMAIL ADDRESS">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact-email">CONTACT EMAIL:</label>
                                <input type="email" id="contact-email" name="contact_email" placeholder="EMAIL">
                            </div>
                            <div class="form-group description-group">
                                <label for="brief-description">BRIEF DESCRIPTION ABOUT ORGANIZATION:</label>
                                <span class="subtext">(you can edit this later)</span>
                                <textarea id="brief-description" name="brief_description" placeholder="Arnold's Sensory Village Center is a community-based initiative to distribute daily meals, prepared with care and compassion, to address the needs of the homeless and urban poor. Our mission is to offer not just food, but hope and human connection to those in need."></textarea>
                            </div>
                            <div class="form-group">
                                <label for="password">PASSWORD:</label>
                                <input type="password" id="password" name="password" placeholder="PASSWORD">
                            </div>
                        </div>

                        <div class="form-row last-row">
                            <div class="form-group">
                                <label for="address">ADDRESS:</label>
                                <input type="text" id="address" name="address" placeholder="ADDRESS">
                            </div>
                            <div class="form-group empty-placeholder">
                                </div>
                            <div class="form-group">
                                <label for="confirm-password">CONFIRM PASSWORD:</label>
                                <input type="password" id="confirm-password" name="confirm_password" placeholder="CONFIRM PASSWORD">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact-number">CONTACT NUMBER:</label>
                                <input type="tel" id="contact-number" name="contact_number" placeholder="09765281337">
                            </div>
                            <div class="form-group empty-placeholder">
                                </div>
                             <div class="form-group empty-placeholder">
                                </div>
                        </div>


                        <div class="terms-checkbox">
                            <input type="checkbox" id="terms" name="terms">
                            <label for="terms">By clicking this button, you agree to the terms and conditions</label>
                        </div>

                        <button type="submit" class="submit-btn">SUBMIT</button>
                    </form>
                </div>
            </div>
            <div class="hero-image-overlay">
                <img src="foodbridge_logo_hero.png" alt="FoodBridge Logo Large" class="large-logo">
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-content">
            <div class="footer-left">
                <a href="index.html">
                    <img src="logo.png" alt="FoodBridge Logo" class="footer-logo">
                </a>
                <p class="copyright">&copy; 2023 FoodBridge. All rights reserved.</p>
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
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <div class="footer-buttons">
                    <button>Donate Surplus Food</button>
                    <button>Request Donations</button>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>