<?php
// Send notification to donor
include '../includes/db.php';

$stmtNotify = $conn->prepare("INSERT INTO notifications (donor_id, donation_id, message) VALUES (?, ?, ?)");
$msg = "Your donation ID #$donation_id has been collected.";
$stmtNotify->bind_param("iis", $donor_id, $donation_id, $msg);
$stmtNotify->execute();
?>
