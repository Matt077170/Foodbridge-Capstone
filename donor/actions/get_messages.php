
<?php
// FILE: donor/actions/get_messages.php
session_start();
include '../../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'donor' || !isset($_GET['recipient_id'])) {
    echo json_encode(['error' => 'Unauthorized or missing data']);
    exit();
}

$donor_id = $_SESSION['user']['id'];
$recipient_id = intval($_GET['recipient_id']);

// Mark messages as read
$update_stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND recipient_id = ?");
$update_stmt->bind_param("ii", $recipient_id, $donor_id);
$update_stmt->execute();
$update_stmt->close();

// Fetch message history
$query = "SELECT sender_id, recipient_id, message_content, created_at
          FROM messages
          WHERE (sender_id = ? AND recipient_id = ?) OR (sender_id = ? AND recipient_id = ?)
          ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $donor_id, $recipient_id, $recipient_id, $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($messages);
$stmt->close();
$conn->close();
?>