<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user']['id'];
$post_id = intval($_POST['post_id']);
$reaction_type = $_POST['reaction_type'] ?? '';

if (!$post_id || !$reaction_type) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

// Check if user already reacted
$check = $conn->prepare("SELECT id FROM post_reactions WHERE user_id = ? AND post_id = ?");
$check->bind_param("ii", $user_id, $post_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    // Update old reaction
    $stmt = $conn->prepare("UPDATE post_reactions SET reaction_type = ? WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("sii", $reaction_type, $user_id, $post_id);
} else {
    // Insert new reaction
    $stmt = $conn->prepare("INSERT INTO post_reactions (post_id, user_id, reaction_type) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $reaction_type);
}
$stmt->execute();


// Return updated counts
$countStmt = $conn->prepare("
    SELECT reaction_type, COUNT(*) as count
    FROM post_reactions
    WHERE post_id = ?
    GROUP BY reaction_type
");
$countStmt->bind_param("i", $post_id);
$countStmt->execute();
$counts = $countStmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode(['success' => true, 'counts' => $counts]);
exit();
?>
