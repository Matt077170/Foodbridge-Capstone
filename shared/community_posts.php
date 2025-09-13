<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$user_name = htmlspecialchars($_SESSION['user']['fullname']);

// Fetch user's profile picture from the database
$stmt_user = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();
$profile_picture = htmlspecialchars($user_data['profile_picture'] ?? '');
$stmt_user->close();

$role = $_SESSION['user']['role'] ?? '';

switch ($role) {
    case 'donor':
        $dashboard_link = '../donor/dashboard.php';
        break;
    case 'organization':
        $dashboard_link = '../organization/dashboard.php';
        break;
    default:
        $dashboard_link = '../login.php'; // fallback if role missing
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Posts</title>
    <script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="cp.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Header Section -->
    <header class="bg-white shadow-sm p-4 sticky top-0 z-10">
        <div class="container mx-auto flex justify-between items-center max-w-3xl">
            <a href="<?= $dashboard_link ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors duration-200">
                &larr; Dashboard
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Community Posts</h1>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Welcome, <?= $user_name ?></span>
                <?php if (!empty($profile_picture)): ?>
                    <img src="../uploads/<?= $profile_picture ?>" alt="Profile Picture" class="w-8 h-8 rounded-full object-cover">
                <?php else: ?>
                    <div class="w-8 h-8 rounded-full bg-gray-400 flex items-center justify-center text-white font-bold text-sm">
                        <?= strtoupper(substr($user_name, 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto p-4 md:p-8 max-w-3xl">
        <div class="space-y-6">
            <?php
            $stmt = $conn->prepare("
                SELECT p.id, p.description, p.featured_image, p.created_at, u.fullname, u.profile_picture
                FROM posts p
                JOIN users u ON p.user_id = u.id
                WHERE p.status = 'approved'
                ORDER BY p.created_at DESC
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            ?>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <!-- Post Card -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="p-4 flex items-center space-x-4">
                            <!-- User Profile Pic -->
                            <div class="w-10 h-10 rounded-full flex-shrink-0">
                                <?php if (!empty($row['profile_picture'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($row['profile_picture']) ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-full bg-gray-400 flex items-center justify-center text-white font-bold text-lg">
                                        <?= strtoupper(substr($row['fullname'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- User Name and Post Date -->
                            <div>
                                <h5 class="font-semibold text-gray-900 leading-tight"><?= htmlspecialchars($row['fullname']) ?></h5>
                                <p class="text-sm text-gray-500">Posted on <?= date('F j, Y', strtotime($row['created_at'])) ?></p>
                            </div>
                        </div>

                        <!-- Post Image -->
                        <?php if (!empty($row['featured_image'])): ?>
                            <div class="w-full h-80 overflow-hidden bg-gray-200">
                                <img src="../uploads/<?= htmlspecialchars($row['featured_image']) ?>" class="w-full h-full object-cover" alt="Post Image">
                            </div>
                        <?php endif; ?>

                        <div class="p-4">
                            <!-- Post Description with Truncation -->
                            <div class="text-gray-700 mb-2">
                                <?php
                                $description = htmlspecialchars($row['description']);
                                $max_length = 100; // Maximum number of characters to display
                                if (strlen($description) > $max_length) {
                                    $truncated_description = substr($description, 0, $max_length) . '...';
                                    echo nl2br($truncated_description);
                                } else {
                                    echo nl2br($description);
                                }
                                ?>
                            </div>

                            <!-- Social Actions -->
                            <div class="flex items-center space-x-4 mt-4 text-gray-500">
                                <!-- View Post Button -->
                                <a href="../shared/view_post.php?id=<?= $row['id'] ?>" class="flex items-center space-x-1 hover:text-blue-500 transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                    </svg>
                                    <span class="text-sm font-medium">View Post</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-12 text-gray-500 text-lg">
                    No approved posts yet.
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
