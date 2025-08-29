<?php
$host = "localhost";
$user = "root";
$pass = ""; // Set your MySQL root password here
$db = "food_donation_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>