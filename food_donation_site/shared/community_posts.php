<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user_name = htmlspecialchars($_SESSION['user']['fullname']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Community Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .post-card img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .post-card {
            margin-bottom: 20px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        .featured-image {
            max-height: 200px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>
<body class="container mt-4">

    <h2 class="mb-4">Welcome, <?= $user_name ?> — Community Posts</h2>
  <?php
$dashboard_link = ($_SESSION['user']['role'] === 'organization') 
    ? '../organization/dashboard.php' 
    : '../donor/dashboard.php';
?>
<a href="<?= $dashboard_link ?>" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <?php
    $stmt = $conn->prepare("
        SELECT p.id, p.description, p.location, p.featured_image, p.created_at, u.fullname 
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.status = 'approved'
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card post-card p-3">
            <h5><?= htmlspecialchars($row['fullname']) ?></h5>
            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>

            <?php if (!empty($row['featured_image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($row['featured_image']) ?>" class="featured-image" alt="Post Image">
            <?php endif; ?>

            <p class="text-muted small mb-1">Posted on <?= date('F j, Y h:i A', strtotime($row['created_at'])) ?></p>
            <a href="../shared/view_post.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">View Full Post</a>
        </div>
    <?php endwhile; ?>

</body>
</html>
