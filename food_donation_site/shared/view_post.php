<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
  header("Location: ../login.php");
  exit();
}

$post_id = intval($_GET['id']);
$user_id = $_SESSION['user']['id'];

// Fetch post + user’s existing reaction (if any)
$stmt = $conn->prepare("
  SELECT p.*, u.fullname,
    (SELECT reaction_type FROM post_reactions WHERE post_id = ? AND user_id = ? LIMIT 1) AS user_reaction
  FROM posts p
  JOIN users u ON u.id = p.user_id
  WHERE p.id = ? AND p.status = 'approved'
");
$stmt->bind_param("iii", $post_id, $user_id, $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
  echo "Post not found or not approved.";
  exit();
}

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

// Fetch comments
$c = $conn->prepare("
  SELECT pc.comment, pc.created_at, u.fullname
  FROM post_comments pc
  JOIN users u ON u.id = pc.user_id
  WHERE pc.post_id = ?
  ORDER BY pc.created_at DESC
");
$c->bind_param("i", $post_id);
$c->execute();
$comments = $c->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <title>View Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .reaction-btn.active {
      font-weight: bold;
      border: 2px solid black;
      background-color: #e0f7fa;
    }
  </style>
</head>
<body class="container py-4">

  <a href="../shared/community_posts.php" class="btn btn-secondary mb-3">← Back to Posts</a>
  <div class="card p-4 mb-4">
    <h4><?= htmlspecialchars($post['fullname']) ?></h4>
    <p><?= nl2br(htmlspecialchars($post['description'])) ?></p>
    <?php if ($post['location']): ?>
      <p><strong>Location:</strong> <?= htmlspecialchars($post['location']) ?></p>
    <?php endif; ?>
  <?php if ($post['featured_image']): ?>
    <div class="featured-image-container">
        <img src="../uploads/<?= htmlspecialchars($post['featured_image']) ?>" alt="Post image">
    </div>
<?php endif; ?>


    <div id="reactions-area" data-post-id="<?= $post_id ?>">
      <button class="btn btn-outline-success reaction-btn <?= $post['user_reaction']=='like'?'active':'' ?>" data-type="like">👍 Like (<?= $reaction_counts['like'] ?? 0 ?>)</button>
      <button class="btn btn-outline-danger reaction-btn <?= $post['user_reaction']=='love'?'active':'' ?>" data-type="love">❤️ Love (<?= $reaction_counts['love'] ?? 0 ?>)</button>
    </div>
  </div>

  <div class="card p-4">
    <h5>Comments</h5>
    <?php while ($r = $comments->fetch_assoc()): ?>
      <div class="mb-3 border-bottom pb-2">
        <strong><?= htmlspecialchars($r['fullname']) ?></strong> • <small><?= $r['created_at'] ?></small><br>
        <?= nl2br(htmlspecialchars($r['comment'])) ?>
      </div>
    <?php endwhile; ?>

    <form method="POST" action="../handlers/comment_post.php">
      <input type="hidden" name="post_id" value="<?= $post_id ?>">
      <textarea name="comment" class="form-control mb-2" placeholder="Write a comment..." required></textarea>
      <button type="submit" class="btn btn-primary btn-sm">Submit Comment</button>
    </form>
  </div>

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
          data.counts.forEach(r => {
            const btn = document.querySelector(`#reactions-area .reaction-btn[data-type="${r.reaction_type}"]`);
            if (btn) btn.textContent = btn.textContent.replace(/\(\d+\)/, `(${r.count})`);
          });
        }
      });
    });
  });
  </script>

</body>
</html>
