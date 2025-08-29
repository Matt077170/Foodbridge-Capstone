<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desc = trim($_POST['description']);
    $location = trim($_POST['location']);
    $image = $_FILES['featured_image'];

    if (empty($desc) || empty($location)) {
        $error = "Please fill out all fields.";
    } else {
        $imageName = '';
        if (!empty($image['name'])) {
            $validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($image['type'], $validTypes)) {
                $error = "Only JPG, PNG or GIF files are allowed.";
            } else {
                $imageName = time() . '_' . basename($image['name']);
                $targetPath = '../uploads/' . $imageName;
                move_uploaded_file($image['tmp_name'], $targetPath);
            }
        }

        if (!$error) {
            $stmt = $conn->prepare("INSERT INTO posts (user_id, description, location, featured_image, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->bind_param("isss", $user_id, $desc, $location, $imageName);
            if ($stmt->execute()) {
                $success = "Post submitted successfully! Waiting for admin approval.";
            } else {
                $error = "Error submitting post.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create a Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

    <h2>Create a Post</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="border p-4 rounded bg-light">
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Featured Image (optional)</label>
            <input type="file" name="featured_image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Submit Post</button>
    </form>

</body>
</html>
