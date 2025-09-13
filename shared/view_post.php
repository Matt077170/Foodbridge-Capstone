<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$post_id = intval($_GET['id']);
$user_id = $_SESSION['user']['id'];

// Fetch post details
$stmt = $conn->prepare("
    SELECT p.*, u.fullname, u.profile_picture
    FROM posts p
    JOIN users u ON u.id = p.user_id
    WHERE p.id = ? AND p.status = 'approved'
");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) {
    echo "Post not found or not approved.";
    exit();
}

// Fetch user’s existing reaction (if any)
$stmt_reaction = $conn->prepare("SELECT reaction_type FROM post_reactions WHERE post_id = ? AND user_id = ? LIMIT 1");
$stmt_reaction->bind_param("ii", $post_id, $user_id);
$stmt_reaction->execute();
$user_reaction = $stmt_reaction->get_result()->fetch_assoc()['reaction_type'] ?? null;
$stmt_reaction->close();

// Fetch reaction counts
$reaction_counts = [];
$res = $conn->prepare("
    SELECT reaction_type, COUNT(*) AS cnt
    FROM post_reactions
    WHERE post_id = ?
    GROUP BY reaction_type
");
$res->bind_param("i", $post_id);
$res->execute();
foreach ($res->get_result()->fetch_all(MYSQLI_ASSOC) as $r) {
    $reaction_counts[$r['reaction_type']] = $r['cnt'];
}
$res->close();

// Fetch comments
$c = $conn->prepare("
    SELECT pc.comment, pc.created_at, u.fullname, u.profile_picture
    FROM post_comments pc
    JOIN users u ON u.id = pc.user_id
    WHERE pc.post_id = ?
    ORDER BY pc.created_at ASC
");
$c->bind_param("i", $post_id);
$c->execute();
$comments = $c->get_result();
$c->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="vp.css">
    <title>View Post</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Header -->
    <header class="bg-white shadow-sm p-4 sticky top-0 z-10">
        <div class="container mx-auto max-w-2xl flex items-center">
            <a href="../shared/community_posts.php" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors duration-200">
                &larr; Back to Posts
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto p-4 md:p-8 max-w-2xl">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Post Header -->
            <div class="p-4 flex items-center space-x-4 border-b border-gray-200">
                <div class="w-12 h-12 rounded-full flex-shrink-0 bg-gray-400 flex items-center justify-center text-white text-xl font-bold">
                    <?php if (!empty($post['profile_picture'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($post['profile_picture']) ?>" alt="Profile" class="w-12 h-12 rounded-full object-cover">
                    <?php else: ?>
                        <?= strtoupper(substr($post['fullname'], 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <h5 class="font-semibold text-gray-900 text-lg"><?= htmlspecialchars($post['fullname']) ?></h5>
                    <p class="text-xs text-gray-500">Posted on <?= date('F j, Y \a\t h:i A', strtotime($post['created_at'])) ?></p>
                </div>
            </div>

            <!-- Post Content -->
            <div class="p-4">
                <p class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars($post['description'])) ?></p>
                <?php if ($post['featured_image']): ?>
                    <div class="w-full max-h-96 overflow-hidden rounded-md bg-gray-200 flex justify-center items-center">
                        <img src="../uploads/<?= htmlspecialchars($post['featured_image']) ?>" class="object-cover w-full h-auto max-h-full" alt="Post image">
                    </div>
                <?php endif; ?>
            </div>

            <!-- Reactions -->
            <div class="flex justify-between items-center p-4 border-t border-gray-200 text-sm text-gray-500">
                <div id="reactions-count-area">
                    <span class="font-medium mr-2"><?= ($reaction_counts['like'] ?? 0) + ($reaction_counts['love'] ?? 0) ?> reactions</span>
                    <span class="font-medium mr-2"> | </span>
                    <span class="font-medium"><?= $comments->num_rows ?> comments</span>
                </div>
            </div>

            <!-- Reaction Buttons -->
            <div id="reactions-area" data-post-id="<?= $post_id ?>" class="p-4 flex justify-around border-t border-gray-200">
                <button class="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-100 transition-colors duration-200 reaction-btn <?= $user_reaction === 'like' ? 'active' : '' ?>" data-type="like">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 9V5c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v10.5c0 .73.54 1.34 1.25 1.48C9.53 17.2 12 15.54 12 15.54V21h8c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2h-6v1z"/>
                    </svg>
                    <span class="text-sm font-medium">Like</span>
                </button>
                <button class="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-100 transition-colors duration-200 reaction-btn <?= $user_reaction === 'love' ? 'active' : '' ?>" data-type="love">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    <span class="text-sm font-medium">Love</span>
                </button>
            </div>

            <!-- Comments Section -->
            <div class="p-4 border-t border-gray-200">
                <h5 class="text-xl font-bold mb-4">Comments</h5>
                <div class="space-y-4 max-h-80 overflow-y-auto">
                    <?php if ($comments->num_rows > 0): ?>
                        <?php while ($r = $comments->fetch_assoc()): ?>
                            <div class="flex space-x-3">
                                <div class="w-8 h-8 rounded-full flex-shrink-0 bg-gray-400 flex items-center justify-center text-white font-bold text-sm">
                                    <?php if (!empty($r['profile_picture'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($r['profile_picture']) ?>" alt="Profile" class="w-8 h-8 rounded-full object-cover">
                                    <?php else: ?>
                                        <?= strtoupper(substr($r['fullname'], 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="bg-gray-100 p-3 rounded-xl flex-grow">
                                    <div class="flex justify-between items-center mb-1">
                                        <strong class="text-sm font-medium"><?= htmlspecialchars($r['fullname']) ?></strong>
                                        <small class="text-xs text-gray-500"><?= date('F j, Y \a\t h:i A', strtotime($r['created_at'])) ?></small>
                                    </div>
                                    <p class="text-sm text-gray-700"><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-500 text-center">Be the first to comment!</p>
                    <?php endif; ?>
                </div>

                <!-- Comment Form -->
                <form method="POST" action="../handlers/comment_post.php" class="mt-4">
                    <input type="hidden" name="post_id" value="<?= $post_id ?>">
                    <div class="flex space-x-2">
                        <textarea name="comment" class="flex-grow form-textarea p-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="Write a comment..." rows="1" required></textarea>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition-colors duration-200">
                            Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.querySelectorAll('#reactions-area .reaction-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const postId = document.getElementById('reactions-area').dataset.postId;
                const type = this.dataset.type;

                fetch('../handlers/react_post.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `post_id=${postId}&reaction_type=${type}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('#reactions-area .reaction-btn').forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        
                        const reactionCountElem = document.getElementById('reactions-count-area');
                        let totalReactions = 0;
                        data.counts.forEach(r => {
                            totalReactions += parseInt(r.count);
                        });
                        reactionCountElem.querySelector('span:first-child').textContent = `${totalReactions} reactions`;
                    }
                });
            });
        });
    </script>
</body>
</html>
