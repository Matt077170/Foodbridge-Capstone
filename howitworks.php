<?php
// Start session if needed (for future login/logout integration)
 session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>How It Works</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
	<link rel="stylesheet" href="css/howitworks.css"/>
	
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

<main class="container py-5">
    <h1 class="text-center fw-bold mb-3">REDUCING FOOD WASTE</h1>
    <h3 class="text-center text-muted mb-5">Practical Tips for Filipinos and Businesses</h3>

    <p>Food waste is an urgent issue in the Philippines, with millions of tons of food being thrown away each year. From surplus produce in local markets to leftover bread in bakeries, food waste not only hurts our environment but also worsens hunger and poverty in the country. As FoodBridge, it's essential that we take steps to reduce food waste in our everyday lives, both as individuals and businesses.</p>

    <h4 class="fw-bold mt-5 mb-3">For Individuals: Simple Steps to Save Food and Help the Planet</h4>
    <h5 class="fw-bold">Plan Your Meals and Shop Smart</h5>
    <ul>
        <li>
            <strong>Make a Weekly Meal Plan:</strong> One of the best ways to avoid food waste is by planning your meals for the week. This helps you buy only what you need, preventing over-purchasing that often leads to food being thrown out. By creating a shopping list and sticking to it, you can avoid impulse buys that you might not use in time.
        </li>
        <li>
            <strong>Shop at Local Markets:</strong> Support your local palengkes (markets), and buy fresh, in-season produce. Local markets tend to have a wide variety of fresh goods, but if you’re buying in bulk, be mindful of your usage to avoid spoilage. By purchasing the exact amount you need, you can reduce the likelihood of food going to waste.
        </li>
    </ul>

    <h5 class="fw-bold">Repurpose Leftovers</h5>
    <ul>
        <li>
            <strong>Be Creative with Leftovers:</strong> Leftover food doesn’t have to go to waste! Try repurposing them into new meals. Leftover rice can be transformed into fried rice, and yesterday’s grilled chicken can become a filling for sandwiches or tacos. Don’t let those extra servings sit in the fridge—think of ways to turn them into new dishes.
        </li>
        <li>
            <strong>Freezing Leftovers:</strong> If you can’t eat leftovers right away, freeze them for later use. Meals like soups, stews, and casseroles can be frozen and reheated, preventing them from going to waste while saving you time and effort in the future.
        </li>
    </ul>
    
    <h5 class="fw-bold">Donate Surplus Food</h5>
    <ul>
        <li>
            <strong>Give Back to the Community:</strong> If you find yourself with surplus food that you won’t be able to consume, consider donating it to a local food bank, charity, or organization like FoodBridge. These initiatives work to redistribute food to people in need, ensuring that it doesn’t go to waste while feeding those who are struggling to access meals.
        </li>
    </ul>

    <h5 class="fw-bold">Composting: Turning Waste into Fertilizer</h5>
    <ul>
        <li>
            <strong>Start Composting at Home:</strong> Food scraps like vegetable peels, coffee grounds, and egg shells don’t need to end up in the trash. Composting these items can help reduce food waste and create nutrient-rich soil for your garden. It's an easy, eco-friendly way to handle food scraps while minimizing landfill waste.
        </li>
    </ul>
    
    <h5 class="fw-bold">Mind Your Portions</h5>
    <ul>
        <li>
            <strong>Serve Smaller Portions:</strong> One common cause of food waste is serving too much food at meals. Try serving smaller portions, and encourage family members to go back for seconds if they’re still hungry. This reduces the chance of leftovers that will eventually go to waste.
        </li>
    </ul>

    <h4 class="fw-bold mt-5 mb-3">For Businesses: Sustainable Practices to Reduce Food Waste</h4>
    <h5 class="fw-bold">Donate Unsold Goods</h5>
    <ul>
        <li>
            <strong>Set up a Donation Program:</strong> For businesses in the food industry, donating unsold goods can make a significant impact. Restaurants, bakeries, and markets can collaborate with organizations like FoodBridge to redistribute surplus food to communities in need. If your bakery has leftover bread or your restaurant has excess food at the end of the day, donating ensures it’s put to good use instead of being thrown away.
        </li>
    </ul>
    
    <h5 class="fw-bold">Implement Food Waste Tracking</h5>
    <ul>
        <li>
            <strong>Track Your Waste:</strong> Tracking food waste in your business can help you identify patterns and find ways to reduce it. If you notice that certain items go unsold frequently, consider adjusting your ordering or production process to prevent overstocking. This practice can help cut down on waste while saving you money.
        </li>
    </ul>
    
    <h5 class="fw-bold">Repurpose and Recycle</h5>
    <ul>
        <li>
            <strong>Repurpose Unsold Food:</strong> Instead of the saying away unsold food, consider repurposing it. For instance, day-old bread can be used to make croutons or bread crumbs, vegetable scraps can be used to make more stock or soup.
        </li>
        <li>
            <strong>Recycling Packaging:</strong> For businesses involved in food production or packaging, reducing packaging waste is equally important. Switch to recyclable or compostable packaging, and encourage customers to bring reusable containers if your business provides take-out, consider eco-friendly options to help reduce packaging waste.
        </li>
    </ul>
    
    <h5 class="fw-bold">Offer Portion Control</h5>
    <ul>
        <li>
            <strong>Offer Customizable Portions:</strong> Give your customers the option to choose smaller or customized portion sizes. This is especially useful for buffet-style or a la carte restaurants where large portions are often left unfinished. Offering smaller portions can reduce food waste both for the customer and the business.
        </li>
    </ul>
    
    <h5 class="fw-bold">Educate Employees and Customers</h5>
    <ul>
        <li>
            <strong>Raise Awareness:</strong> As your business, educate both employees and customers about the importance of reducing food waste. Train staff to properly handle food to avoid spoilage, and encourage customers to be mindful of the portions they order. You can even create a campaign to highlight your business’s commitment to sustainability and food waste reduction.
        </li>
    </ul>

    <h4 class="fw-bold mt-5">The Bottom line: Every Little Effort Counts</h4>
    <p>Reducing food waste in the Philippines doesn’t require a complete overhaul of your habits—every small change helps. Whether you’re a busy individual trying to make the most of your grocery shopping, or a business owner working to improve sustainability, taking conscious steps toward reducing food waste can have a significant impact on the environment, economy, and society.</p>
    
    <p>From meal planning and repurposing leftovers to donating food and improving waste management in businesses, every action contributes to creating a culture of sustainability. As we continue to face challenges like hunger and food insecurity in the Philippines, reducing food waste is one simple but powerful way we can all make a difference. Let’s work together to reduce food waste, support local communities, and create a more sustainable future for all. The version focuses on reducing food waste while providing relevant, practical tips for both individuals and businesses in the Philippines, so we know you’d like any further adjustments.</p>
</main>

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