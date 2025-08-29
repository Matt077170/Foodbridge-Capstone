<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .register-links {
            text-align: center;
            margin-top: 100px;
        }
        .register-links a {
            display: inline-block;
            margin: 20px;
            padding: 12px 24px;
            background-color: #3498db;
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
        }
        .register-links a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="register-links">
        <h2>Register as</h2>
        <a href="Donor/register_donor.php">Donor</a>
        <a href="Organization/register_org.php">Organization</a>
    </div>
</body>
</html>
