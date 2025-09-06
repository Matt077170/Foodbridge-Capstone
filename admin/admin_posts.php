<?php
session_start();
include '../includes/db.php';

// Check for admin role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$current_user_id = $_SESSION['user']['id'];
$current_user_role = $_SESSION['user']['role'];

// Pagination and Filtering Logic
$limit = 10; // Number of items per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;

// Get search and filter parameters from URL
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$current_tab = isset($_GET['tab']) && in_array($_GET['tab'], ['pending', 'history']) ? $_GET['tab'] : 'pending';

// Handle approval/rejection/undo actions
if (isset($_GET['action'], $_GET['id'])) {
    $post_id = intval($_GET['id']);
    $action = $_GET['action'];
    $admin_user_id = $_SESSION['user']['id'];
    $admin_role = $_SESSION['user']['role'];

    // Get current status before updating
    $stmt = $conn->prepare("SELECT status FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->bind_result($old_status);
    $stmt->fetch();
    $stmt->close();

    $new_status = '';
    $log_description = '';

    // A simple transaction to ensure the update and log action are atomic
    $conn->begin_transaction();

    try {
        if ($action === 'approve') {
            $new_status = 'approved';
            $log_description = "Approved post with ID: " . $post_id;
        } elseif ($action === 'reject') {
            // Changed status to 'rejected' for consistency.
            $new_status = 'rejected'; 
            $log_description = "Denied post with ID: " . $post_id;
        } elseif ($action === 'undo') {
            $new_status = 'pending';
            $log_description = "Undid approval/denial for post with ID: " . $post_id;
        }

        // Update
        $stmt = $conn->prepare("UPDATE posts SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $post_id);
        $stmt->execute();
        $stmt->close();

        // Log the action after a successful update
        if (!empty($log_description) && $admin_user_id) {
            // Assuming log_action is defined in db.php or another included file
            log_action($conn, $admin_user_id, $log_description, $admin_role);
        }

        $conn->commit();
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        // Log the error for debugging
        error_log("Failed to process post action: " . $e->getMessage());
    }

    // Redirect to the same page with current filters and tab
    $redirect_url = "admin_posts.php?tab=" . urlencode($current_tab);
    if ($search_query) {
        $redirect_url .= "&q=" . urlencode($search_query);
    }
    if ($role_filter) {
        $redirect_url .= "&role=" . urlencode($role_filter);
    }
    if ($page > 1) {
        $redirect_url .= "&page=" . $page;
    }

    header("Location: " . $redirect_url);
    exit();
}

// Build the dynamic WHERE clause for the query
$conditions = [];
$params = [];
$param_types = '';

if ($current_tab === 'pending') {
    $conditions[] = "p.status = 'pending'";
} else {
    // Modified to include both 'approved' and 'rejected' to show all history
    $conditions[] = "p.status IN ('approved', 'rejected')";
}

if (!empty($search_query)) {
    $conditions[] = "(p.description LIKE ? OR u.fullname LIKE ?)";
    $search_term = "%" . $search_query . "%";
    $params[] = $search_term;
    $params[] = $search_term;
    $param_types .= 'ss';
}

if (!empty($role_filter)) {
    $conditions[] = "u.role = ?";
    $params[] = $role_filter;
    $param_types .= 's';
}

$where_clause = "WHERE " . implode(" AND ", $conditions);

// First query: get total count for pagination
$count_sql = "SELECT COUNT(*) FROM posts p JOIN users u ON p.user_id = u.id " . $where_clause;
$count_stmt = $conn->prepare($count_sql);

if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}
$count_stmt->execute();
$count_stmt->bind_result($total_results);
$count_stmt->fetch();
$count_stmt->close();

$total_pages = ceil($total_results / $limit);

// Second query: get paginated and filtered data
$sql = "SELECT p.*, u.fullname, u.role FROM posts p JOIN users u ON p.user_id = u.id " . $where_clause . " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

$pagination_params = $params;
$pagination_params[] = $limit;
$pagination_params[] = $start;
$pagination_param_types = $param_types . 'ii';

if (!empty($pagination_params)) {
    $stmt->bind_param($pagination_param_types, ...$pagination_params);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Approve Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="adminpost.css"/>
</head>
<body>

<main class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Post Approvals</h2>
        <a href="dashboard.php" class="btn btn-secondary btn-back-modern">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            <span class="d-none d-md-inline ms-2">Back to Dashboard</span>
        </a>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <form method="GET" action="admin_posts.php" class="d-flex flex-grow-1 me-2 mb-2 mb-md-0">
            <div class="input-group">
                <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                </svg></span>
                <input type="text" name="q" class="form-control" placeholder="Search by description or author" value="<?= htmlspecialchars($search_query) ?>">
            </div>
            <select name="role" class="form-select mx-2" style="width: 150px;">
                <option value="" <?= $role_filter === '' ? 'selected' : '' ?>>All Roles</option>
                <option value="donor" <?= $role_filter === 'donor' ? 'selected' : '' ?>>Donor</option>
                <option value="organization" <?= $role_filter === 'organization' ? 'selected' : '' ?>>Organization</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="admin_posts.php?tab=<?= htmlspecialchars($current_tab) ?>" class="btn btn-secondary ms-2">Clear</a>
        </form>
    </div>

    <ul class="nav nav-tabs nav-tabs-modern mb-4" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $current_tab === 'pending' ? 'active' : '' ?>" href="?tab=pending" role="tab" aria-selected="<?= $current_tab === 'pending' ? 'true' : 'false' ?>">Pending Posts</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $current_tab === 'history' ? 'active' : '' ?>" href="?tab=history" role="tab" aria-selected="<?= $current_tab === 'history' ? 'true' : 'false' ?>">History</a>
        </li>
    </ul>

    <div class="tab-content pt-3">
        <div class="tab-pane fade show active" role="tabpanel">
            <h4 class="mb-3"><?= $current_tab === 'pending' ? 'Pending Post Approvals' : 'Approved & Rejected Posts' ?></h4>
            <div class="row g-4">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-animated h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($row['fullname']) ?> (<?= ucfirst(htmlspecialchars($row['role'])) ?>)</h5>
                                    <p class="card-text">
                                        <?= nl2br(htmlspecialchars($row['description'])) ?>
                                    </p>
                                    <?php if ($row['featured_image']): ?>
                                        <div class="image-preview mb-2">
                                            <img src="../uploads/<?= htmlspecialchars($row['featured_image']) ?>" class="img-fluid rounded">
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($current_tab === 'history'): ?>
                                        <p><strong>Status:</strong> <span class="badge rounded-pill <?= $row['status'] === 'approved' ? 'bg-success' : 'bg-danger' ?>"><?= ucfirst(htmlspecialchars($row['status'])) ?></span></p>
                                    <?php endif; ?>
                                    <p class="text-muted"><small><?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?></small></p>

                                    <div class="d-flex justify-content-between mt-3">
                                        <?php if ($current_tab === 'pending'): ?>
                                            <a href="?action=approve&id=<?= $row['id'] ?>&tab=<?= htmlspecialchars($current_tab) ?>" class="btn btn-success btn-sm btn-action">✅ Approve</a>
                                            <a href="?action=reject&id=<?= $row['id'] ?>&tab=<?= htmlspecialchars($current_tab) ?>" class="btn btn-danger btn-sm btn-action">❌ Reject</a>
                                        <?php else: ?>
                                            <a href="?action=undo&id=<?= $row['id'] ?>&tab=<?= htmlspecialchars($current_tab) ?>" class="btn btn-warning btn-sm btn-action">↩️ Undo</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="alert alert-info text-center mt-3">No posts found matching your criteria.</p>
                <?php endif; ?>
            </div>

            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php
                            $query_params = [
                                'tab' => $current_tab,
                                'q' => $search_query,
                                'role' => $role_filter,
                                'page' => $i
                            ];
                        ?>
                        <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query($query_params) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
