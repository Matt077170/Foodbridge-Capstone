<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $image = '';

    if (!empty($_FILES['image']['name'])) {
        $imgName = time() . "_" . basename($_FILES['image']['name']);
        $uploadPath = '../uploads/' . $imgName;
        $ext = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $image = $imgName;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, role, description, location, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $role, $description, $location, $image);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create a Post</title>
</head>
<body>
    <h2>Create a Post</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Description:</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Location:</label><br>
        <input type="text" name="location"><br><br>

        <label>Featured Image:</label><br>
        <input type="file" name="image" accept="image/*"><br><br>

        <button type="submit">Submit Post</button>
    </form>
</body>
</html>
