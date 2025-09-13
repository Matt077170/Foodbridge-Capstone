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

// Fetch the logged-in user's details for the header
$stmt_user = $conn->prepare("SELECT fullname, profile_picture FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_info = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desc = trim($_POST['description']);
    $image = $_FILES['featured_image'];

    if (empty($desc)) {
        $error = "Please fill out the description.";
    } else {
        $imageName = '';
        if (!empty($image['name'])) {
            $validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($image['type'], $validTypes)) {
                $error = "Only JPG, PNG or GIF files are allowed.";
            } else {
                $imageName = time() . '_' . basename($image['name']);
                $targetPath = '../../uploads/' . $imageName; // Corrected path
                move_uploaded_file($image['tmp_name'], $targetPath);
            }
        }

        if (!$error) {
            $stmt = $conn->prepare("INSERT INTO posts (user_id, description, featured_image, status) VALUES (?, ?, ?, 'pending')");
            $stmt->bind_param("iss", $user_id, $desc, $imageName);
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-xl mx-auto p-4 md:p-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 relative">
            
            <a href="dashboard.php" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>

            <h2 class="text-3xl font-bold mb-6 text-center text-gray-900">Create Post</h2>
            
            <?php if ($error): ?>
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert"><?= $error ?></div>
            <?php elseif ($success): ?>
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert"><?= $success ?></div>
            <?php endif; ?>

            <div class="flex items-center space-x-4 mb-6">
                <div class="w-12 h-12 rounded-full flex-shrink-0 bg-gray-400 flex items-center justify-center text-white text-xl font-bold">
                    <?php if (!empty($user_info['profile_picture'])): ?>
                        <img src="<?= htmlspecialchars($user_info['profile_picture']) ?>" alt="Profile" class="w-12 h-12 rounded-full object-cover">
                    <?php else: ?>
                        <?= strtoupper(substr($user_info['fullname'], 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <h5 class="font-semibold text-gray-900 text-lg"><?= htmlspecialchars($user_info['fullname']) ?></h5>
                    <p class="text-sm text-gray-500">What's on your mind?</p>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <textarea name="description" class="w-full form-textarea p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 text-sm" placeholder="Write a description..." rows="5" required></textarea>
                </div>

                <div class="mb-6 border border-gray-300 rounded-xl p-3 flex items-center justify-between">
                    <label class="text-sm font-medium text-gray-500">Add to your post</label>
                    <div class="flex space-x-2">
                        <label for="featured_image" class="cursor-pointer text-gray-600 hover:text-blue-600 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.69a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                            </svg>
                            <input type="file" name="featured_image" id="featured_image" class="hidden" accept="image/*">
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-colors duration-200">
                    Post
                </button>
            </form>
        </div>
    </div>
</body>
</html>
