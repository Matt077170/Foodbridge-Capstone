<?php
// FILE: organization/actions/get_conversations.php
session_start();
include '../../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'organization') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$org_id = $_SESSION['user']['id'];

// Get all unique donors the organization has messaged with
$query = "SELECT u.id, u.fullname, u.profile_picture
          FROM users u
          JOIN (
              SELECT DISTINCT recipient_id as user_id FROM messages WHERE sender_id = ?
              UNION
              SELECT DISTINCT sender_id as user_id FROM messages WHERE recipient_id = ?
          ) AS conversations ON u.id = conversations.user_id
          WHERE u.role = 'donor'";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $org_id, $org_id);
$stmt->execute();
$result = $stmt->get_result();
$conversations = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($conversations);
$stmt->close();
$conn->close();
?>
