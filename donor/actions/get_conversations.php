
<?php
// FILE: donor/actions/get_conversations.php
session_start();
include '../../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'donor') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$donor_id = $_SESSION['user']['id'];

// Get all unique users (organizations) the donor has messaged or received messages from
$query = "SELECT u.id, u.fullname, u.profile_picture
          FROM users u
          JOIN (
              SELECT DISTINCT recipient_id as user_id FROM messages WHERE sender_id = ?
              UNION
              SELECT DISTINCT sender_id as user_id FROM messages WHERE recipient_id = ?
          ) AS conversations ON u.id = conversations.user_id
          WHERE u.role = 'organization'";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $donor_id, $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$conversations = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($conversations);
$stmt->close();
$conn->close();
?>