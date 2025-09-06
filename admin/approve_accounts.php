<?php
session_start();
include '../includes/db.php'; // This now includes the single, corrected log_action function.

// Check for admin role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Pagination and Filtering Logic
$limit = 10; // Number of items per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;

// Get search and filter parameters from URL
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$current_tab = isset($_GET['tab']) && in_array($_GET['tab'], ['pending', 'history']) ? $_GET['tab'] : 'pending';

// Handle Approve / Reject / Undo actions
if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    $admin_user_id = $_SESSION['user']['id'];
    $admin_role = $_SESSION['user']['role'];

    // Use a prepared statement to securely update the user status
    $status_update_sql = "UPDATE users SET status = ? WHERE id = ?";
    $status_stmt = $conn->prepare($status_update_sql);
    if ($status_stmt === false) {
        error_log("Status update prepare failed: " . $conn->error);
    } else {
        $status = '';
        $log_message = '';
        switch ($action) {
            case 'approve':
                $status = 'approved';
                $log_message = "Approved user account with ID: $id";
                break;
            case 'reject':
                $status = 'denied';
                $log_message = "Rejected user account with ID: $id";
                break;
            case 'undo':
                $status = 'pending';
                $log_message = "Undid status change for user account with ID: $id";
                break;
            default:
                // Invalid action, do nothing
                break;
        }

        if ($status) {
            $status_stmt->bind_param("si", $status, $id);
            if ($status_stmt->execute()) {
                // Now calling the corrected log_action function from db.php with all three parameters
                log_action($conn, $admin_user_id, $log_message, $admin_role);
            } else {
                error_log("Status update execute failed: " . $status_stmt->error);
            }
        }
        $status_stmt->close();
    }

    // Redirect to the same page with current filters and tab
    $redirect_url = "approve_accounts.php?tab=" . $current_tab;
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
    $conditions[] = "status = 'pending'";
} else {
    $conditions[] = "status IN ('approved', 'denied')";
}

if (!empty($search_query)) {
    $conditions[] = "(fullname LIKE ? OR email LIKE ? OR address LIKE ?)";
    $search_term = "%" . $search_query . "%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $param_types .= 'sss';
}

if (!empty($role_filter)) {
    $conditions[] = "role = ?";
    $params[] = $role_filter;
    $param_types .= 's';
}

$where_clause = "WHERE " . implode(" AND ", $conditions);

// First query: get total count for pagination
$count_sql = "SELECT COUNT(*) FROM users " . $where_clause;
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
$sql = "SELECT * FROM users " . $where_clause . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
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
    <title>Approve Accounts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="approveaccounts.css"/>
</head>
<body>

<header class="navbar-header">
    <div class="navbar-content">
        <div class="logo-section">
            <img src="images/logo.png" alt="Food Bridge Logo" class="navbar-logo">
        </div>
    </div>
</header>

<div class="container py-4">
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

    <h2 class="mb-4">Account Approvals</h2>

    <!-- Search and Filter Form -->
    <div class="d-flex mb-3">
        <form method="GET" action="approve_accounts.php" class="d-flex flex-grow-1">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($current_tab) ?>">
            <input type="text" name="q" class="form-control me-2" placeholder="Search by name, email, or address" value="<?= htmlspecialchars($search_query) ?>">
            <select name="role" class="form-select me-2" style="width: 150px;">
                <option value="" <?= $role_filter === '' ? 'selected' : '' ?>>All Roles</option>
                <option value="donor" <?= $role_filter === 'donor' ? 'selected' : '' ?>>Donor</option>
                <option value="organization" <?= $role_filter === 'organization' ? 'selected' : '' ?>>Organization</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="approve_accounts.php?tab=<?= htmlspecialchars($current_tab) ?>" class="btn btn-secondary ms-2">Clear</a>
        </form>
    </div>

    <!-- Bootstrap Tabs -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $current_tab === 'pending' ? 'active' : '' ?>" href="?tab=pending" role="tab" aria-selected="<?= $current_tab === 'pending' ? 'true' : 'false' ?>">Pending Accounts</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?= $current_tab === 'history' ? 'active' : '' ?>" href="?tab=history" role="tab" aria-selected="<?= $current_tab === 'history' ? 'true' : 'false' ?>">History</a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content pt-3">
        <!-- Tab content is now dynamic based on the PHP query results -->
        <div class="tab-pane fade show active" role="tabpanel">
            <h4 class="mb-3"><?= $current_tab === 'pending' ? 'Pending Account Approvals' : 'Approved & Rejected Accounts' ?></h4>
            <div class="row">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($row['fullname']) ?></h5>
                                    <p class="card-text">
                                        <strong>Email:</strong> <?= htmlspecialchars($row['email']) ?><br>
                                        <strong>Role:</strong> <?= ucfirst(htmlspecialchars($row['role'])) ?><br>
                                        <?php if ($current_tab === 'history'): ?>
                                            <strong>Status:</strong> <span class="badge <?= $row['status'] === 'approved' ? 'bg-success' : 'bg-danger' ?>"><?= ucfirst($row['status']) ?></span><br>
                                        <?php endif; ?>
                                        <strong>Birthday:</strong> <?= htmlspecialchars($row['birthday']) ?><br>
                                        <strong>Contact:</strong> <?= htmlspecialchars($row['contact']) ?><br>
                                        <strong>Address:</strong> <?= htmlspecialchars($row['address']) ?><br>
                                    </p>

                                    <?php if ($row['valid_id']): ?>
                                        <p><strong>Valid ID:</strong></p>
                                        <img src="../uploads/ids/<?= htmlspecialchars($row['valid_id']) ?>" class="img-fluid rounded mb-2" style="max-height:200px; object-fit:cover;">
                                    <?php endif; ?>

                                    <?php if ($row['selfie']): ?>
                                        <p><strong>Selfie with ID:</strong></p>
                                        <img src="../uploads/selfies/<?= htmlspecialchars($row['selfie']) ?>" class="img-fluid rounded mb-2" style="max-height:200px; object-fit:cover;">
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-between mt-3">
                                        <?php if ($current_tab === 'pending'): ?>
                                            <a href="?action=approve&id=<?= $row['id'] ?>&tab=<?= htmlspecialchars($current_tab) ?>" class="btn btn-success btn-sm">✅ Approve</a>
                                            <a href="?action=reject&id=<?= $row['id'] ?>&tab=<?= htmlspecialchars($current_tab) ?>" class="btn btn-danger btn-sm">❌ Reject</a>
                                        <?php else: ?>
                                            <a href="?action=undo&id=<?= $row['id'] ?>&tab=<?= htmlspecialchars($current_tab) ?>" class="btn btn-warning btn-sm">↩️ Undo</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted text-center mt-3">No accounts found matching your criteria.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination Links -->
            <nav aria-label="Page navigation">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
