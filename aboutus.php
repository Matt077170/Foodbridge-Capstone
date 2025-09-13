<?php
// Start session if needed (for future login/logout integration)
 session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
	<link rel="stylesheet" href="css/aboutus.css"/>
	
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>

<body>

<!-- Navigation -->
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
                <li class="nav-item">
                    <a href="login.php" class="btn btn-warning ms-3">Log-in</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="main-content">
        <section class="mission-section">
            <h1>Our Mission</h1>
            <p>At the heart of our mission is a deep commitment to ending hunger and reducing food waste in our communities. Every day, countless meals are thrown away while many individuals and families struggle with food insecurity. We believe that no one should have to choose between going hungry and feeding their loved ones. Our mission is to bridge that gap by rescuing edible surplus food and redirecting it to those who need it most.</p>
            <p>We work closely with local restaurants, markets, farms, and individuals to collect food that would otherwise go to waste. This includes fresh produce, baked goods, and non-perishable items—all of which are sorted, stored, and safely delivered to soup kitchens, shelters, and food-insecure households. By building strong partnerships, we ensure a reliable and efficient flow of donations, turning potential waste into life-sustaining nourishment.</p>
            <p>Education is also a vital part of our mission. We strive to raise awareness about the environmental and social impact of food waste through workshops, campaigns, and community events. By empowering people with knowledge, we inspire everyday actions—like mindful consumption and responsible food storage—that contribute to long-term change. When people understand how their habits affect others and the planet, they become more intentional in their choices.</p>
            <p>Our mission extends beyond food—it’s about restoring dignity, hope, and health to those we serve. We believe that access to nutritious meals is a basic human right, not a privilege. Every meal we provide is a step toward a more compassionate society, where no one is forgotten or left behind. We envision a future where everyone has a place at the table, and where waste is no longer part of the equation.</p>
            <p>Together with our volunteers, donors, and partners, we are creating a movement built on generosity, sustainability, and community. As we continue to grow, our mission remains clear: to rescue food, feed people, and build a future where hunger and waste no longer exist side by side. With your support, we can turn that vision into reality—one meal at a time.</p>
        </section>
</main>

<section class="how-we-started animate__animated animate__fadeIn">
    <div class="container">
        <h1 class="display-5 fw-bold text-success text-center mb-4">How we Started</h1>
        <p class="lead">
            <p>Our journey began with a simple yet powerful realization: while many people in our community were struggling to find their next meal, large amounts of perfectly good food were being thrown away every day. What started as a few individuals volunteering at local soup kitchens quickly turned into a mission to bridge this heartbreaking gap between waste and hunger.
        </p>
        <p>
            In the early days, we began small—collecting leftover bread from bakeries, unsold produce from markets, and extra meals from local events. With no formal structure, just determination and compassion, we packed the donations into our own cars and hand-delivered them to families and shelters in need. The gratitude we received from those we helped showed us just how big the need really was.
        </p>
        <p>
            Word spread quickly, and more volunteers, donors, and partners began to join our cause. Local businesses reached out to offer regular food donations, and community members started helping with pickups, sorting, and delivery. It became clear that this was more than just an act of kindness—it was the start of a movement to fight hunger and reduce waste in a sustainable way.
        </p>
        <p>
            As we grew, so did our vision. We developed systems to track donations, maintain food safety standards, and expand our reach. We hosted awareness campaigns and educational events to inspire others to take action in their own homes and workplaces. Our team, once made up of just a few friends, became a dedicated network of changemakers united by a shared purpose.
        </p>
        <p>
            Today, we remain grounded in those early moments—when we first saw how one rescued meal could make a difference. Our beginnings remind us that meaningful change often starts small, with people who care enough to take action. We’re proud of how far we’ve come, and we’re even more excited for the impact we can continue to make together.
        </p>
    </div>
</section>

<footer class="new-footer">
    <div class="container">
        <div class="row align-items-center py-4">
            <div class="col-md-4 d-flex align-items-start footer-left">
                <img src="images/logo.png" alt="FoodBridge Logo" class="footer-logo-new me-3">
                <div class="d-flex flex-column justify-content-center">
                    <p class="mb-0 text-white-50">900 San Marcelino St. Ermita, Manila, 1000 Metro Manila</p>
                    <p class="mb-0 text-white-50">020 178 910 675</p>
                    <a href="mailto:hello@foodbridgemail.ph" class="footer-link-email">hello@foodbridgemail.ph</a>
                </div>
            </div>

            <div class="col-md-4 footer-center">
                <ul class="list-unstyled">
                    <li><a href="#" class="footer-nav-link">Food Waste Sources</a></li>
                    <li><a href="#" class="footer-nav-link">Soup Kitchens in Manila</a></li>
                </ul>
            </div>

            <div class="col-md-4 d-flex flex-column align-items-end footer-right">
                <div class="social-icons-new mb-3">
                    <a href="#" class="social-icon facebook"></a>
                    <a href="#" class="social-icon instagram"></a>
                    <a href="#" class="social-icon linkedin"></a>
                </div>
                <div class="d-flex">
                    <a href="privacy.php" class="footer-policy-link">Privacy Policy</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-start text-white-50 small-copyright">
                &copy; FoodBridge 2025
            </div>
        </div>
    </div>
	</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>