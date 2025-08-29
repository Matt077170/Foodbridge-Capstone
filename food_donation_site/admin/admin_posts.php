<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Filtering, searching, sorting
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'pending';
$sort = in_array($_GET['sort'] ?? '', ['created_at', 'fullname']) ? $_GET['sort'] : 'created_at';
$order = ($_GET['order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

$where = "WHERE 1=1";
$params = [];
$types = "";

if (in_array($status, ['pending', 'approved', 'rejected'])) {
    $where .= " AND p.status = ?";
    $params[] = $status;
    $types .= "s";
}

if ($search !== "") {
    $where .= " AND (p.description LIKE ? OR p.location LIKE ? OR p.tags LIKE ? OR u.fullname LIKE ?)";
    $params[] = $params[] = $params[] = $params[] = "%$search%";
    $types .= "ssss";
}

// Count total
$countStmt = $conn->prepare("SELECT COUNT(*) FROM posts p JOIN users u ON p.user_id=u.id $where");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = $countStmt->get_result()->fetch_row()[0];
$totalPages = ceil($total / $limit);

// Get posts
$params[] = $limit;
$params[] = $offset;
$types .= "ii";
$sql = "SELECT p.*, u.fullname, u.role FROM posts p JOIN users u ON p.user_id=u.id $where ORDER BY $sort $order LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin | Manage Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top { max-height: 180px; object-fit: cover; }
        .modal-img { max-height: 300px; object-fit: cover; }
        .opacity-50 { opacity: 0.5; }
    </style>
</head>
<body class="container py-4">
    <h2>Admin – Manage Community Posts</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <form method="get" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="search" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All</option>
                <?php foreach(['pending', 'approved', 'rejected'] as $st): ?>
                    <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="sort" class="form-select">
                <option value="created_at" <?= $sort==='created_at'?'selected':'' ?>>Date</option>
                <option value="fullname" <?= $sort==='fullname'?'selected':'' ?>>User</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="order" class="form-select">
                <option value="DESC" <?= $order==='DESC'?'selected':'' ?>>Newest</option>
                <option value="ASC" <?= $order==='ASC'?'selected':'' ?>>Oldest</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <?php if ($result->num_rows === 0): ?>
        <p class="text-muted">No posts found.</p>
    <?php endif; ?>

    <?php $i = 0; while ($row = $result->fetch_assoc()): $i++; ?>
        <div class="card mb-3 shadow-sm" id="post-<?= $row['id'] ?>">
            <div class="row g-0">
                <?php if ($row['featured_image']): ?>
                <div class="col-md-3">
                    <img src="../uploads/<?= htmlspecialchars($row['featured_image']) ?>" class="img-fluid card-img-top" alt="Image">
                </div>
                <?php endif; ?>
                <div class="col-md-9">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['fullname']) ?> (<?= $row['role'] ?>)</h5>
                        <p><?= htmlspecialchars(mb_strimwidth($row['description'], 0, 100, "...")) ?></p>
                        <p><strong>Status:</strong> <span id="badge-<?= $row['id'] ?>" class="badge bg-secondary"><?= ucfirst($row['status']) ?></span></p>
                        <p><small><?= date('F j, Y, g:i a', strtotime($row['created_at'])) ?></small></p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal<?= $i ?>">View</button>
                            <?php if ($row['status'] === 'pending'): ?>
                                <button class="btn btn-success btn-sm update-status" data-id="<?= $row['id'] ?>" data-status="approve">Approve</button>
                                <button class="btn btn-danger btn-sm update-status" data-id="<?= $row['id'] ?>" data-status="reject">Reject</button>
                            <?php endif; ?>
                            <button class="btn btn-outline-warning btn-sm undo-btn d-none" data-id="<?= $row['id'] ?>">Undo</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="viewModal<?= $i ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Post by <?= htmlspecialchars($row['fullname']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <?php if ($row['featured_image']): ?>
                            <img src="../uploads/<?= htmlspecialchars($row['featured_image']) ?>" class="img-fluid modal-img mb-3" alt="Image">
                        <?php endif; ?>
                        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                        <?php if ($row['location']): ?>
                            <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                        <?php endif; ?>
                        <?php if ($row['tags']): ?>
                            <p><strong>Tags:</strong> <?= htmlspecialchars($row['tags']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

    <?php if ($totalPages > 1): ?>
    <nav>
        <ul class="pagination mt-4">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>

    <!-- Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="toast-msg" class="toast text-bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toast-body">Action completed.</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function showToast(message) {
        document.getElementById("toast-body").textContent = message;
        new bootstrap.Toast(document.getElementById("toast-msg")).show();
    }

    const actionHistory = {};

    document.querySelectorAll('.update-status').forEach(button => {
        button.addEventListener('click', () => {
            const postId = button.dataset.id;
            const status = button.dataset.status;
            const badge = document.getElementById(`badge-${postId}`);
            const undoBtn = document.querySelector(`.undo-btn[data-id='${postId}']`);
            const card = document.getElementById(`post-${postId}`);

            fetch('../handlers/update_post_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `post_id=${postId}&status=${status}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    badge.textContent = status.toUpperCase();
                    badge.className = `badge ${status === 'approved' ? 'bg-success' : 'bg-danger'}`;
                    card.classList.add('opacity-50');
                    undoBtn.classList.remove('d-none');
                    actionHistory[postId] = 'pending';
                    showToast(`Post ${postId} ${status}d`);
                } else {
                    alert(data.error || 'Failed.');
                }
            });
        });
    });

    document.querySelectorAll('.undo-btn').forEach(button => {
        button.addEventListener('click', () => {
            const postId = button.dataset.id;
            const badge = document.getElementById(`badge-${postId}`);
            const card = document.getElementById(`post-${postId}`);

            fetch('../handlers/update_post_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `post_id=${postId}&status=pending`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    badge.textContent = 'PENDING';
                    badge.className = 'badge bg-secondary';
                    card.classList.remove('opacity-50');
                    button.classList.add('d-none');
                    showToast(`Undo: Post ${postId} is pending`);
                }
            });
        });
    });
    </script>
</body>
</html>
