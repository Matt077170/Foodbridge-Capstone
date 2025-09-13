<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <header class="main-header">
        <div class="logo">
            <img src="images/logo.png" alt="Foodbridge Logo">
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="#">ABOUT US</a></li>
                <li><a href="#">HOW IT WORKS</a></li>
                <li><a href="#">ABOUT FOOD WASTE</a></li>
            </ul>
        </nav>
    </header>

    <div class="main-content">
        <div class="register-section">
            <div class="section-header">
                <h2>Register as</h2>
                <p>Choose the role that best describes how you'll join our mission.</p>
            </div>
            <div class="register-cards-container">
                <a href="Donor/register_donor.php" class="modern-card donor-card">
                    <h3>Donor</h3>
                    <p>Donate surplus food to support local communities and reduce waste.</p>
                </a>

                <a href="Organization/register_org.php" class="modern-card organization-card">
                    
                    <h3>Organization</h3>
                    <p>Register your non-profit to receive valuable food donations.</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>