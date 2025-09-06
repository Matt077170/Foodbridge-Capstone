<?php
// FILENAME audit_trail.php
session_start();
include '../includes/db.php'; // Make sure this path is correct.

// This page requires an 'admin' role to access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Pagination logic
$limit = 20; // Number of records per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Get search and filter parameters from the URL
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';

// --- Build the dynamic SQL query ---
$conditions = [];
$params = [];
$param_types = '';

// The base query now includes a JOIN to the 'users' table
$base_sql = "FROM audit_trail AS at JOIN users AS u ON at.user_id = u.id";

// Add search condition if a query is provided
if (!empty($search_query)) {
    // Search by user name, action, or role
    $conditions[] = "(u.fullname LIKE ? OR at.action LIKE ? OR at.role LIKE ?)";
    $search_term = "%" . $search_query . "%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $param_types .= 'sss';
}

// Add role filter condition
if (!empty($role_filter)) {
    $conditions[] = "at.role = ?";
    $params[] = $role_filter;
    $param_types .= 's';
}

// Construct the WHERE clause
$where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// --- First query: Get total count for pagination ---
$count_sql = "SELECT COUNT(at.id) " . $base_sql . " " . $where_clause;
$count_stmt = $conn->prepare($count_sql);

// Bind parameters if they exist
if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}
$count_stmt->execute();
$count_stmt->bind_result($total_records);
$count_stmt->fetch();
$count_stmt->close();

$total_pages = ceil($total_records / $limit);

// --- Second query: Get the data for the current page ---
$sql = "SELECT at.id, u.fullname, at.action, at.role, at.timestamp " . $base_sql . " " . $where_clause . " ORDER BY at.timestamp DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

// Add limit and offset to the parameters and their types
$pagination_params = $params;
$pagination_params[] = $limit;
$pagination_params[] = $offset;
$pagination_param_types = $param_types . 'ii';

// Bind parameters for the main query
$stmt->bind_param($pagination_param_types, ...$pagination_params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Trail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="audit.css"/>
</head>
<body>

<header class="navbar-header">
        <div class="navbar-content">
            <div class="logo-section">
                <img src="images/logo.png" alt="Food Bridge Logo" class="navbar-logo">
            </div>
        </div>
    </header>

<main class="container py-5">
    <div class="d-flex justify-content-end align-items-center mb-4">
        <a href="dashboard.php" class="btn btn-secondary btn-back-modern">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            <span class="d-none d-md-inline ms-2">Back to Dashboard</span>
        </a>
    </div>

    <div class="card card-modern p-4 shadow-lg">
        <form method="GET" action="audit_trail.php" class="row g-3 align-items-end mb-4 form-animated">
            <div class="col-md-6">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control form-control-modern" id="search" name="q" placeholder="Search by user name, action, or role" value="<?= htmlspecialchars($search_query) ?>">
            </div>
            <div class="col-md-3">
                <label for="role" class="form-label">Filter by Role</label>
                <select class="form-select form-control-modern" id="role" name="role">
                    <option value="" <?= $role_filter === '' ? 'selected' : '' ?>>All Roles</option>
                    <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="donor" <?= $role_filter === 'donor' ? 'selected' : '' ?>>Donor</option>
                    <option value="organization" <?= $role_filter === 'organization' ? 'selected' : '' ?>>Organization</option>
                </select>
            </div>
            <div class="col-md-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-animated me-2">Apply Filters</button>
                <a href="audit_trail.php" class="btn btn-secondary btn-animated">Clear</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-striped table-animated">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Admin Name</th>
                        <th scope="col">Action</th>
                        <th scope="col">Role</th>
                        <th scope="col">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['fullname']) ?></td>
                                <td><?= htmlspecialchars($row['action']) ?></td>
                                <td><?= htmlspecialchars($row['role']) ?></td>
                                <td><?= htmlspecialchars($row['timestamp']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No audit trail records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center pagination-animated">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?q=<?= htmlspecialchars($search_query) ?>&role=<?= htmlspecialchars($role_filter) ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                        <a class="page-link" href="?q=<?= htmlspecialchars($search_query) ?>&role=<?= htmlspecialchars($role_filter) ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?q=<?= htmlspecialchars($search_query) ?>&role=<?= htmlspecialchars($role_filter) ?>&page=<?= $page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
