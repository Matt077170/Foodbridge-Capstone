<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user']['id'];
    $comment = trim($_POST['comment']);

    if (!empty($comment)) {
      $stmt = $conn->prepare("INSERT INTO post_comments (post_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $comment);
        $stmt->execute();
    }

    header("Location: ../shared/view_post.php?id=" . $post_id);
    exit();
}
?>
