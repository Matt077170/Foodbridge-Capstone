<?php
include '../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['fullname'];
    $org_id = $_POST['org_id'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (fullname, org_id, address, contact, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?, 'organization', 'pending')");
    $stmt->bind_param("ssssss", $name, $org_id, $address, $contact, $email, $password);
    $stmt->execute();

    header("Location: ../login.php");
    exit();
}
?>
 <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign up for Organization</title>
  <link rel="stylesheet" href="../css/register_org.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <nav class="navbar">
      <div class="logo">
  <a href="../index.php" style="text-decoration: none; color: inherit;">
    <img src="../img/foodbridge_logo.png" alt="FoodBridge Logo">
  </a>
</div>
      <ul>
        <li><a href="#">ABOUT US</a></li>
        <li><a href="#">HOW IT WORKS</a></li>
        <li><a href="#">ABOUT FOOD WASTE</a></li>
      </ul>
    </nav>
  </header>
<form method="post">
  <section class="intro">
    <h2><span class="highlight">Make an</span> Impact Today</h2>
    <br>
    <p>Join FoodBridge and be part of a movement to fight food waste and feed communities.<br>It only takes a minute to start making a difference.</p>
  </section>
<section class="form-container">
  <h2>Sign up for Organization</h2>
  <form method="post" action="process_register.php">
    <div class="form-group">
      <label for="fullname">Organization Name:</label>
      <input type="text" name="fullname" id="fullname" required>
    </div>
    <div class="form-group">
      <label for="org_id">Organization ID:</label>
      <input type="text" name="org_id" id="org_id" required>
    </div>
    <div class="form-group">
      <label for="address">Address:</label>
      <input type="text" name="address" id="address" required>
    </div>
    <div class="form-group">
      <label for="contact">Contact No.:</label>
      <input type="text" name="contact" id="contact" required>
    </div>
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" name="email" id="email" required>
    </div>
    <div class="form-group">
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required>
    </div>
    <div class="form-group">
      <label for="confirm">Confirm Password:</label>
      <input type="password" name="confirm" id="confirm" required>
    </div>
      <div class="terms-checkbox">
      <input type="checkbox" id="terms" name="terms">
      <label for="terms">By clicking this button, you agree to the <a href="#" id="open-terms-modal">terms and conditions</a></label>
    </div>
    <div class="submit-btn">
      <button type="submit">Register</button>
    </div>
  </form>
</section>
<div id="termsModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5);">
    <div class="modal-content" style="background:#fff; margin:10vh auto; padding:2rem; border-radius:10px; max-width:500px; position:relative;">
        <span class="close-button" style="position:absolute; top:10px; right:20px; font-size:2rem; cursor:pointer;">&times;</span>
        <h3>TERMS AND CONDITIONS</h3>
        <p>By creating an account, you agree to these terms and conditions and acknowledge that you are solely responsible for all activities that occur under your account, whether or not authorized by you.</p>
        <p>You are responsible for maintaining the confidentiality of your account password and any other security information associated with your account.</p>
        <p>You agree to notify us immediately if you suspect any unauthorized use of your account or any security breach.</p>
        <p>You agree to use the platform in accordance with all applicable laws and regulations.</p>
        <p>You agree not to engage in any activity that could disrupt or interfere with the platform or its users.</p>
        <p>You agree not to upload, post, or transmit any content that is illegal, harmful, abusive, or offensive.</p>
        <p>We may terminate or suspend your account immediately, without prior notice or liability, for any reason, including but not limited to, if you violate these Terms and Conditions.</p>
        <p>You may terminate your account at any time by following the instructions provided on the platform.</p>
        <p>Upon termination, you will no longer have access to your account and any associated data.</p>
        <p>These Terms and Conditions shall be governed by and construed in accordance with the laws of [Metro Manila, Philippines], without regard to its conflict of law provisions.</p>
        <button class="confirm-button" id="confirm-terms-button" style="margin-top:1rem;">Confirm</button>
    </div>
</div>

<!-- Place this script before </body> -->
<script>
// JavaScript for Modal functionality (matches TermsnConditions.php)
const termsModal = document.getElementById('termsModal');
const openTermsModalLink = document.getElementById('open-terms-modal');
const closeButton = document.querySelector('.close-button');
const confirmTermsButton = document.getElementById('confirm-terms-button');
const termsCheckbox = document.getElementById('terms');

openTermsModalLink.addEventListener('click', function(event) {
    event.preventDefault();
    termsModal.style.display = 'block';
});

closeButton.addEventListener('click', function() {
    termsModal.style.display = 'none';
});

confirmTermsButton.addEventListener('click', function() {
    termsCheckbox.checked = true;
    termsModal.style.display = 'none';
});

window.addEventListener('click', function(event) {
    if (event.target == termsModal) {
        termsModal.style.display = 'none';
    }
});
</script>
</body>
</html>


