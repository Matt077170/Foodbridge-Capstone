<?php
// Start session if needed (for future login/logout integration)
 session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About FoodWaste</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
	<link rel="stylesheet" href="css/foodwaste.css"/>
	
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

<section class="food-waste-matters py-5">
    <div class="container">
        <h1 class="text-center fw-bold text-success mb-3">WHY FOOD WASTE MATTERS</h1>
        <h3 class="text-center text-muted mb-5">A Call for Change in the Philippines</h3>

        <p class="mb-5">Food waste is a growing concern around the world, and the Philippines is no exception. In a country where millions of people go hungry every day, it’s heartbreaking to know that a staggering amount of food is wasted each year. While we tend to focus on the food that goes into our kitchens, we must also consider the surplus food that gets discarded long before it even reaches our tables.</p>

        <div class="row text-center mb-5">
            <div class="col-md-4">
                <img src="images/img1.png" alt="Woman at a local market" class="img-fluid rounded">
            </div>
            <div class="col-md-4">
                <img src="images/img2.png" alt="Children with a food vendor" class="img-fluid rounded">
            </div>
            <div class="col-md-4">
                <img src="images/img3.png" alt="A pile of discarded food" class="img-fluid rounded">
            </div>
        </div>

        <div class="content-text">
            <h4 class="fw-bold text-success">The Palengke: A Hub of Unused Potential</h4>
            <p>In local markets, or palengkes, unsold fruits, vegetables, and fish are often discarded by the end of the day, either because they spoil too quickly or because they didn’t sell. Farmers and vendors usually overstock in anticipation of demand, but unfortunately, demand can fluctuate. As a result, these fresh goods often sit unsold, and when they do, they end up in the trash.</p>
            <p>Imagine the piles of fresh produce—ripe bananas, juicy tomatoes, or leafy greens—that are wasted simply because they couldn’t be sold in time. These are foods that could nourish families in need, but instead, they’re thrown away, contributing to the growing problem of food waste in the country.</p>

            <h4 class="fw-bold text-success mt-4">The Environmental Impact: Wasting More Than Just Food</h4>
            <p>It’s not just the food itself that’s wasted. Wasting food means wasting valuable resources like water, energy, and labor—resources that the Philippines already struggles to manage. Consider the fact that it takes about 1,800 gallons of water to produce just one pound of beef, or the fuel needed to transport food across the country. When food is wasted, so too are these precious resources, exacerbating environmental challenges like water scarcity and energy shortages.</p>

            <h4 class="fw-bold text-success mt-4">The Social Cost: Millions in Need, Yet Food Is Thrown Away</h4>
            <p>While food is discarded, millions of Filipinos face hunger and malnutrition. In Metro Manila alone, there are thousands of low-income families who struggle to put meals on the table every day. In a country where so many go without, the thought of wasting food is particularly hard to swallow. Instead of going to waste, surplus food could be directed to the people who need it most, bridging the gap between abundance and scarcity.</p>

            <h4 class="fw-bold text-success mt-4">The Economic Loss: A Price We Can’t Afford</h4>
            <p>The economic implications of food waste are just as alarming. The Philippines loses billions of pesos annually due to food waste. Vendors in palengkes and bakeries experience financial loss when unsold food is tossed out, contributing to economic strain. The money spent on producing, storing, and transporting food is essentially thrown away, affecting not only small business owners but the larger economy as well. By finding ways to reduce food waste, the country can save money and boost its economy.</p>

            <h4 class="fw-bold text-success mt-4">The Solution: Reducing Food Waste Through Collective Effort</h4>
            <p>The good news is that solutions to food waste are within our reach. Initiatives like FoodBridge are helping to combat this issue by connecting donors—such as local markets, bakeries, and households—with organizations that can redistribute surplus food to communities in need. Through these efforts, food that would have gone to waste can be used to feed the hungry, reducing waste and alleviating hunger at the same time.</p>
            <p>By supporting these programs, we can make a difference in the fight against food waste. It’s not just about feeding the hungry—it’s about creating a more sustainable and equitable food system. Together, we can change the way we think about food waste, and more importantly, how we act on it.</p>
        </div>
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