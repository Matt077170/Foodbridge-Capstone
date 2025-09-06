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
    $comment = trim($_POST['comment']);

    if ($comment !== '') {
       $stmt = $conn->prepare("INSERT INTO post_comments (post_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $comment);
        $stmt->execute();
    }

    header("Location: view_posts.php");
    exit();
}
?>
