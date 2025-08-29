<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['id'];
    $post_id = intval($_POST['post_id']);
    $reaction = $_POST['reaction'];

    // Valid reactions
    $valid = ['like', 'love', 'care', 'wow'];
    if (!in_array($reaction, $valid)) {
        die("Invalid reaction.");
    }

    // Check if user already reacted
    $check = $conn->prepare("SELECT id FROM post_reactions WHERE post_id = ? AND user_id = ?");
    $check->bind_param("ii", $post_id, $user_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        // Update existing reaction
        $stmt = $conn->prepare("UPDATE post_reactions SET reaction = ?, created_at = NOW() WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $reaction, $post_id, $user_id);
    } else {
        // Insert new reaction
        $stmt = $conn->prepare("INSERT INTO post_reactions (post_id, user_id, reaction) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $reaction);
    }

    $stmt->execute();
    header("Location: view_posts.php");
    exit();
}
?>
