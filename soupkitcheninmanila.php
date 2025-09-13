<?php
// Start session if needed (for future login/logout integration)
 session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Soup Kitchen in Manila</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
	<link rel="stylesheet" href="css/skm.css"/>
	
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body>
    <header>
        <div class="logo">
		<a class="navbar-brand" href="index.php">
            <img src="images/logo.png" alt="FoodBridge Logo" class="logo-image">
        </div>
        <nav>
            <a href="#" class="my-account-btn">My Account</a>
        </nav>
    </header>

    <main>
        <div class="hero-section">
            <div class="hero-text">
                <h1>Soup Kitchen in Manila</h1>
                <p>Support local soup kitchens & charities by donation or volunteering.</p>
                <div class="search-bar">
                    <input type="text" placeholder="Search by name or location...">
                    <button class="all-charities-btn">All Charities</button>
                    <button class="search-btn">Search</button>
                </div>
            </div>
            <div class="hero-image">
                <img src="images/map.png" alt="Map of the Philippines">
            </div>
        </div>

        <section class="charity-grid">
            <div class="charity-card">
                <img src="images/img4.png" alt="Missionaries of Charity">
                <div class="card-content">
                    <h3>Missionaries of Charity</h3>
                    <p>Elderly People</p>
                </div>
            </div>
            <div class="charity-card">
                <img src="images/img5.png" alt="Tuluyan San Benito">
                <div class="card-content">
                    <h3>Tuluyan San Benito</h3>
                    <p>(Shelter for the Homeless)</p>
                </div>
            </div>
            <div class="charity-card">
                <img src="images/img6.png" alt="AJ Kalinga Center">
                <div class="card-content">
                    <h3>AJ Kalinga Center</h3>
                </div>
            </div>
            <div class="charity-card">
                <img src="images/img7.png" alt="Don Bosco Youth Center">
                <div class="card-content">
                    <h3>Don Bosco Youth Center</h3>
                </div>
            </div>
            <div class="charity-card">
                <img src="images/img8.png" alt="Kapwa Kusina">
                <div class="card-content">
                    <h3>Kapwa Kusina</h3>
                </div>
            </div>
            <div class="charity-card">
                <img src="images/img9.png" alt="Maria Clara Community">
                <div class="card-content">
                    <h3>Maria Clara Community</h3>
                </div>
            </div>
            <div class="charity-card">
                <img src="images/img10.png" alt="FAMCOHSEF Outreach">
                <div class="card-content">
                    <h3>FAMCOHSEF Outreach</h3>
                </div>
            </div>
            <div class="charity-card">
                <img src="images/img11.png" alt="National Children's Hospital">
                <div class="card-content">
                    <h3>National Children's Hospital</h3>
                </div>
            </div>
            <div class="charity-card">
                <img src="path/to/haven-for-women.jpg" alt="Haven for Women">
                <div class="card-content">
                    <h3>Haven for Women</h3>
                </div>
            </div>
            <div class="charity-card">
                <img src="path/to/haven-for-children.jpg" alt="Haven for Children, Home for Juvenile Boys">
                <div class="card-content">
                    <h3>Haven for Children, Home for Juvenile Boys</h3>
                </div>
            </div>
            <div class="charity-card">
                <img src="path/to/caritas-manila.jpg" alt="Caritas Manila, Church of the Poor">
                <div class="card-content">
                    <h3>Caritas Manila: Church of the Poor</h3>
                </div>
            </div>
            <div class="charity-card">
                <img src="path/to/bilibid-prison.jpg" alt="Bilibid Prison">
                <div class="card-content">
                    <h3>Bilibid Prison</h3>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
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
                    <li><a href="soupkitcheninmanila.php" class="footer-nav-link">Soup Kitchens in Manila</a></li>
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


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</footer>
</body>
</html>