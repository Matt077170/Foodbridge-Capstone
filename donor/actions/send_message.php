<?php
// FILE: donor/actions/send_message.php
session_start();
include '../../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'donor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$donor_id = $_SESSION['user']['id'];
$recipient_id = $data['recipient_id'] ?? 0;
$message = trim($data['message'] ?? '');

if (empty($recipient_id) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Recipient and message cannot be empty.']);
    exit();
}
//UPDATED
$stmt = $conn->prepare("INSERT INTO messages (sender_id, recipient_id, message_content, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $donor_id, $recipient_id, $message);

if ($stmt->execute()) {
    $message_id = $stmt->insert_id;
    echo json_encode([
        'success' => true,
        'message_id' => $message_id,
        'sender_id' => $donor_id,
        'recipient_id' => $recipient_id,
        'message_content' => $message,
        'created_at' => date('Y-m-d H:i:s')
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
}

$stmt->close();
$conn->close();
?>