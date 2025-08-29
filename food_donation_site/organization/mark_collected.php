<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'organization') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['collect_id'])) {
    $donation_id = intval($_POST['collect_id']);

    // Update the donation status
    $stmt = $conn->prepare("UPDATE donations SET status = 'collected' WHERE id = ?");
    $stmt->bind_param("i", $donation_id);
    $stmt->execute();

    // Fetch the donor_id and insert notification
    $stmtDonor = $conn->prepare("SELECT donor_id FROM donations WHERE id = ?");
    $stmtDonor->bind_param("i", $donation_id);
    $stmtDonor->execute();
    $resultDonor = $stmtDonor->get_result();
    if ($rowDonor = $resultDonor->fetch_assoc()) {
        $donor_id = $rowDonor['donor_id'];

        $stmtNotify = $conn->prepare("INSERT INTO notifications (donor_id, donation_id, message) VALUES (?, ?, ?)");
        $msg = "Your donation ID #$donation_id has been collected.";
        $stmtNotify->bind_param("iis", $donor_id, $donation_id, $msg);
        $stmtNotify->execute();
    }

    header("Location: dashboard.php?view=history");
    exit();
}
