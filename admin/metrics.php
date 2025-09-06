<?php
session_start();
include '../includes/db.php';
header('Content-Type: application/json');

// Donations count
$donations = [
  "pending" => $conn->query("SELECT COUNT(*) FROM donations WHERE status='pending'")->fetch_row()[0],
  "approved" => $conn->query("SELECT COUNT(*) FROM donations WHERE status='approved'")->fetch_row()[0],
  "collected" => $conn->query("SELECT COUNT(*) FROM donations WHERE status='collected'")->fetch_row()[0],
  "rejected" => $conn->query("SELECT COUNT(*) FROM donations WHERE status='rejected'")->fetch_row()[0],
];

// Top 5 Donors (Yearly)
$donors = [];
$res = $conn->query("
    SELECT u.fullname, COUNT(d.id) AS total
    FROM donations d
    JOIN users u ON u.id = d.donor_id
    WHERE d.status='collected' AND YEAR(d.created_at)=YEAR(CURDATE())
    GROUP BY d.donor_id
    ORDER BY total DESC LIMIT 5
");
while($r = $res->fetch_assoc()) $donors[] = $r;

// Top 5 Organizations (Yearly)
$orgs = [];
$res = $conn->query("
    SELECT u.fullname, COUNT(d.id) AS total
    FROM donations d
    JOIN users u ON u.id = d.organization_id
    WHERE d.status='collected' AND YEAR(d.created_at)=YEAR(CURDATE())
    GROUP BY d.organization_id
    ORDER BY total DESC LIMIT 5
");
while($r = $res->fetch_assoc()) $orgs[] = $r;

echo json_encode([
  "donations" => $donations,
  "top_donors" => $donors,
  "top_orgs" => $orgs
]);
