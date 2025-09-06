<?php
include '../includes/db.php';

$donors = $conn->query("SELECT COUNT(*) c FROM users WHERE role='donor'")->fetch_assoc()['c'];
$orgs = $conn->query("SELECT COUNT(*) c FROM users WHERE role='organization'")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) c FROM donation_items WHERE status='pending'")->fetch_assoc()['c'];
$approved = $conn->query("SELECT COUNT(*) c FROM donation_items WHERE status='approved'")->fetch_assoc()['c'];

$html = "
<div class='row text-center mb-3'>
  <div class='col-md-3 mb-3'><div class='card shadow-sm'><div class='card-body'><h4>$donors</h4><p>Donors</p></div></div></div>
  <div class='col-md-3 mb-3'><div class='card shadow-sm'><div class='card-body'><h4>$orgs</h4><p>Organizations</p></div></div></div>
  <div class='col-md-3 mb-3'><div class='card shadow-sm'><div class='card-body'><h4>$pending</h4><p>Pending Donations</p></div></div></div>
  <div class='col-md-3 mb-3'><div class='card shadow-sm'><div class='card-body'><h4>$approved</h4><p>Approved Donations</p></div></div></div>
</div>
";

echo json_encode([
  "html"=>$html,
  "donors"=>$donors,
  "orgs"=>$orgs,
  "pending"=>$pending,
  "approved"=>$approved
]);
