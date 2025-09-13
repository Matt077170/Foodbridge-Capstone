<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'organization') {
    header("Location: ../login.php");
    exit();
}

$org_id = (int)$_SESSION['user']['id'];
$view = $_GET['view'] ?? '';

// Fetch organization details
$org_info = [];
$org_query = $conn->prepare("SELECT fullname, profile_picture FROM users WHERE id = ?");
$org_query->bind_param("i", $org_id);
$org_query->execute();
$org_result = $org_query->get_result();
if ($org_result->num_rows > 0) {
    $org_info = $org_result->fetch_assoc();
}
$org_query->close();

$counts = [];
$chartMonths = [];
$chartPosts = [];
$chartDonations = [];
$donors_for_messages = [];

if ($view === '') {
    // === Stats cards ===
    $q1 = $conn->prepare("SELECT COUNT(*) FROM donations WHERE organization_id = ? AND status = 'approved'");
    $q1->bind_param("i", $org_id);
    $q1->execute(); $q1->bind_result($approved_donations); $q1->fetch(); $q1->close();

    $q2 = $conn->prepare("SELECT COUNT(*) FROM donations WHERE organization_id = ? AND status = 'collected'");
    $q2->bind_param("i", $org_id);
    $q2->execute(); $q2->bind_result($collected_donations); $q2->fetch(); $q2->close();

    $q3 = $conn->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ? AND status = 'pending'");
    $q3->bind_param("i", $org_id);
    $q3->execute(); $q3->bind_result($pending_posts); $q3->fetch(); $q3->close();

    $q4 = $conn->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ? AND status = 'approved'");
    $q4->bind_param("i", $org_id);
    $q4->execute(); $q4->bind_result($approved_posts); $q4->fetch(); $q4->close();

    $counts = [
        'approved_donations' => (int)$approved_donations,
        'collected_donations' => (int)$collected_donations,
        'pending_posts' => (int)$pending_posts,
        'approved_posts' => (int)$approved_posts,
    ];

    // === Chart data: last 6 months (including current) ===
    // Build the last 6 YYYY-MM keys with human labels
    $last6 = [];
    for ($i = 5; $i >= 0; $i--) {
        $ym = date('Y-m', strtotime("-$i months"));
        $label = date('M Y', strtotime("-$i months"));
        $last6[$ym] = ['label' => $label, 'posts' => 0, 'donations' => 0];
    }

    // Posts approved per month
    $posts_stmt = $conn->prepare("
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS cnt
        FROM posts
        WHERE user_id = ? AND status = 'approved'
          AND created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), '%Y-%m-01')
        GROUP BY ym
        ORDER BY ym ASC
    ");
    $posts_stmt->bind_param("i", $org_id);
    $posts_stmt->execute();
    $posts_res = $posts_stmt->get_result();
    while ($r = $posts_res->fetch_assoc()) {
        if (isset($last6[$r['ym']])) $last6[$r['ym']]['posts'] = (int)$r['cnt'];
    }
    $posts_stmt->close();
// --- [NEW] TOP 5 DONORS OF THE YEAR ---
$top_donors = [];
$current_year = date('Y');
$top_donors_stmt = $conn->prepare("
    SELECT
        u.fullname AS donor_name,
        COUNT(d.id) AS donation_count
    FROM donations d
    JOIN users u ON d.donor_id = u.id
    WHERE d.organization_id = ?
      AND d.status = 'collected'
      AND YEAR(d.created_at) = ?
    GROUP BY d.donor_id, u.fullname
    ORDER BY donation_count DESC
    LIMIT 5
");
$top_donors_stmt->bind_param("is", $org_id, $current_year);
$top_donors_stmt->execute();
$top_donors_result = $top_donors_stmt->get_result();
while ($row = $top_donors_result->fetch_assoc()) {
    $top_donors[] = $row;
}
$top_donors_stmt->close();
    // Donations collected per month
    $don_stmt = $conn->prepare("
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS cnt
        FROM donations
        WHERE organization_id = ? AND status = 'collected'
          AND created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), '%Y-%m-01')
        GROUP BY ym
        ORDER BY ym ASC
    ");
    $don_stmt->bind_param("i", $org_id);
    $don_stmt->execute();
    $don_res = $don_stmt->get_result();
    while ($r = $don_res->fetch_assoc()) {
        if (isset($last6[$r['ym']])) $last6[$r['ym']]['donations'] = (int)$r['cnt'];
    }
    $don_stmt->close();

    foreach ($last6 as $ym => $row) {
        $chartMonths[] = $row['label'];
        $chartPosts[] = $row['posts'];
        $chartDonations[] = $row['donations'];
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <title>Organization Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap / Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body { background: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #343a40; padding: 1rem; }
        .sidebar a { display: block; padding: .6rem; color: #fff; text-decoration: none; margin-bottom: .4rem; border-radius: 5px; }
        .sidebar a:hover, .sidebar a.active { background: #0d6efd; }
        .content { padding: 2rem; }
        .profile-thumbnail { width: 60px; height: 60px; object-fit: cover; border: 2px solid #fff; }
        .image-thumbnail { width: 40px; height: 40px; object-fit: cover; border-radius: 5px; cursor: pointer; transition: transform 0.2s; }

        /* Messaging */
        .chat-container { display: flex; height: 75vh; border: 1px solid #ddd; border-radius: 5px; background: #fff; }
        .conversation-list { border-right: 1px solid #ddd; overflow-y: auto; }
        .conversation-list .list-group-item { cursor: pointer; border-radius: 0; border-left: 0; border-right: 0; }
        .conversation-list .list-group-item.active { background-color: #0d6efd; color: white; }
        .chat-window { display: flex; flex-direction: column; }
        .chat-header { padding: 1rem; border-bottom: 1px solid #ddd; background: #f8f9fa; }
        .chat-body { flex-grow: 1; padding: 1rem; overflow-y: auto; display: flex; flex-direction: column-reverse; }
        .message { max-width: 70%; padding: 0.5rem 1rem; margin-bottom: 0.5rem; border-radius: 15px; }
        .message.sent { background-color: #0d6efd; color: white; align-self: flex-end; border-bottom-right-radius: 0; }
        .message.received { background-color: #e9ecef; color: #333; align-self: flex-start; border-bottom-left-radius: 0; }
        .chat-footer { padding: 1rem; border-top: 1px solid #ddd; }

        .chat-action-btn { background-color: #007bff; color: white; border: none; border-radius: 5px; padding: 10px 15px; cursor: pointer; width: 100%; margin-top: 10px; }
        .chat-action-btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="col-md-2 sidebar d-none d-md-block">
        <div class="d-flex align-items-center mb-4">
            <img src="<?= htmlspecialchars($org_info['profile_picture'] ?? '../uploads/profile_pictures/default-avatar.png') ?>" alt="Profile Picture" class="profile-thumbnail rounded-circle me-3">
            <h5 class="text-white mb-0"><?= htmlspecialchars($org_info['fullname'] ?? 'Organization') ?></h5>
        </div>
        <hr class="text-white-50">
        <a href="dashboard.php" class="<?= $view === '' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
        <a href="dashboard.php?view=history" class="<?= $view === 'history' ? 'active' : '' ?>"><i class="fas fa-history me-2"></i> Donations Received</a>
        <a href="dashboard.php?view=messages" class="<?= $view === 'messages' ? 'active' : '' ?>"><i class="fas fa-comments me-2"></i> Messages</a>
        <a href="../shared/community_posts.php"><i class="fas fa-users me-2"></i> Community Posts</a>
        <a href="create_post.php"><i class="fas fa-edit me-2"></i> Create Post</a>
       
        <a href="profile.php"><i class="fas fa-user-circle me-2"></i> Profile</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <!-- Content -->
    <div class="col-md-10 content">
        <h2>Organization Dashboard</h2>
        <hr>

        <?php if ($view === ''): ?>
            <h3 class="mb-4">Donations & Posts Overview</h3>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card text-center shadow-sm"><div class="card-body">
                        <h6 class="text-muted">Approved Donations</h6>
                        <h3 class="text-success"><?= $counts['approved_donations'] ?></h3>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm"><div class="card-body">
                        <h6 class="text-muted">Collected Donations</h6>
                        <h3 class="text-primary"><?= $counts['collected_donations'] ?></h3>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm"><div class="card-body">
                        <h6 class="text-muted">Pending Posts</h6>
                        <h3 class="text-warning"><?= $counts['pending_posts'] ?></h3>
                    </div></div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm"><div class="card-body">
                        <h6 class="text-muted">Approved Posts</h6>
                        <h3 class="text-success"><?= $counts['approved_posts'] ?></h3>
                    </div></div>
                </div>
            </div>
<div class="card shadow-sm p-4 mb-4">
    <h4 class="card-title mb-3">🏆 Top Donors of <?= date('Y') ?></h4>
    <?php if (!empty($top_donors)): ?>
        <ul class="list-group list-group-flush">
            <?php
            $rank = 1;
            $trophies = [
                1 => 'text-warning', // Gold
                2 => 'text-secondary', // Silver
                3 => 'text-danger'  // Bronze (using a Bootstrap 'danger' color that looks like bronze)
            ];
            foreach ($top_donors as $donor):
            ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold me-2">#<?= $rank ?></span>
                        <?php if (isset($trophies[$rank])): ?>
                            <i class="fas fa-trophy <?= $trophies[$rank] ?> me-2"></i>
                        <?php endif; ?>
                        <?= htmlspecialchars($donor['donor_name']) ?>
                    </div>
                    <span class="badge bg-primary rounded-pill">
                        <?= $donor['donation_count'] ?> Donation<?= $donor['donation_count'] > 1 ? 's' : '' ?>
                    </span>
                </li>
            <?php
            $rank++;
            endforeach;
            ?>
        </ul>
    <?php else: ?>
        <p class="text-center text-muted mt-3">No collected donations found for this year yet.</p>
    <?php endif; ?>
</div>

<div class="card shadow-sm p-4 mb-4">
    <h4 class="card-title mb-3">Monthly Activity (Last 6 Months)</h4>
    <canvas id="monthlyChart"></canvas>
</div>
          

        <?php elseif ($view === 'history'): ?>
            <h3 class="mb-4">Donations Received (Approved)</h3>

            <div class="card shadow-sm p-4 mb-4">
                <form method="get" action="" class="row g-3">
                    <input type="hidden" name="view" value="history">
                    <div class="col-md-5">
                        <input type="text" name="search" placeholder="Search by Donor Name"
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <select name="sort" class="form-select">
                            <?php
                                $sort = $_GET['sort'] ?? 'created_at';
                                $order = $_GET['order'] ?? 'DESC';
                            ?>
                            <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>Date</option>
                            <option value="donor_name" <?= $sort === 'donor_name' ? 'selected' : '' ?>>Donor Name</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="order" class="form-select">
                            <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>Descending</option>
                            <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                    </div>
                </form>
            </div>

            <?php
                // Pagination + search (approved only)
                $search = $_GET['search'] ?? '';
                $sortWhitelist = ['created_at', 'donor_name'];
                $orderWhitelist = ['ASC', 'DESC'];
                $sort = in_array($sort, $sortWhitelist, true) ? $sort : 'created_at';
                $order = in_array($order, $orderWhitelist, true) ? $order : 'DESC';
                $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                $limit = 10;
                $offset = ($page - 1) * $limit;

                $query = "
                    SELECT SQL_CALC_FOUND_ROWS
                        d.id AS donation_id,
                        d.created_at,
                        d.delivery_mode,
                        d.caption,
                        d.status,
                        u.fullname AS donor_name,
                        dc.within_expiration_date, dc.properly_stored, dc.no_damaged_packaging,
                        dc.fresh_not_rotten, dc.no_contamination, dc.packaging_intact, dc.food_safely_prepared,
                        GROUP_CONCAT(DISTINCT CONCAT(i.item_type, ':', i.quantity, ':', i.expiration_date) SEPARATOR '|') AS items_data,
                        GROUP_CONCAT(DISTINCT img.image_path) AS images
                    FROM donations d
                    LEFT JOIN donation_conditions dc ON d.id = dc.donation_id
                    LEFT JOIN donation_items i ON d.id = i.donation_id
                    LEFT JOIN donation_images img ON d.id = img.donation_id
                    LEFT JOIN users u ON d.donor_id = u.id
                   WHERE d.organization_id = ? AND d.status IN ('approved', 'collected')
                ";

                $types = "i";
                $params = [$org_id];

                if ($search) {
                    $query .= " AND (u.fullname LIKE ?)";
                    $types .= "s";
                    $like = "%$search%";
                    $params[] = $like;
                }

                // Safe ORDER BY
                $orderBy = $sort === 'donor_name' ? "u.fullname" : "d.created_at";
                $query .= " GROUP BY d.id ORDER BY $orderBy $order LIMIT ? OFFSET ?";
                $types .= "ii";
                $params[] = $limit;
                $params[] = $offset;

                $stmt = $conn->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                $total_result = $conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc()['total'];
                $total_pages = (int)ceil($total_result / $limit);
                $stmt->close();
            ?>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Donation ID</th>
                            <th>Donor Name</th>
                            <th>Date</th>
                            <th>Images</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="donationTableBody">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr id="donation-<?= $row['donation_id'] ?>">
                                <td>#<?= $row['donation_id'] ?></td>
                                <td><?= htmlspecialchars($row['donor_name'] ?? 'N/A') ?></td>
                                <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <?php
                                    if ($row['images']) {
                                        $imgs = array_map('trim', explode(",", $row['images']));
                                        foreach ($imgs as $img) {
                                            $src = '../' . ltrim($img, '/');
                                            echo "<img src='{$src}' alt='Donation Image' class='image-thumbnail me-1' data-bs-toggle='modal' data-bs-target='#imageModal' data-image-url='{$src}'>";
                                        }
                                    } else {
                                        echo "<span class='text-muted'>N/A</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="badge bg-success rounded-pill"><?= htmlspecialchars(ucfirst($row['status'])) ?></span>
                                </td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-info text-white details-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#donationDetailsModal"
                                        data-delivery-mode="<?= htmlspecialchars($row['delivery_mode']) ?>"
                                        data-caption="<?= htmlspecialchars($row['caption']) ?>"
                                        data-donation-conditions='<?= json_encode([
                                            'within_expiration_date' => $row['within_expiration_date'],
                                            'properly_stored' => $row['properly_stored'],
                                            'no_damaged_packaging' => $row['no_damaged_packaging'],
                                            'fresh_not_rotten' => $row['fresh_not_rotten'],
                                            'no_contamination' => $row['no_contamination'],
                                            'packaging_intact' => $row['packaging_intact'],
                                            'food_safely_prepared' => $row['food_safely_prepared']
                                        ]) ?>'
                                        data-items='<?= json_encode(array_map(function($item) {
                                            $parts = explode(":", $item);
                                            return [
                                                "type" => $parts[0] ?? 'N/A',
                                                "quantity" => $parts[1] ?? 'N/A',
                                                "expiry" => $parts[2] ?? 'N/A'
                                            ];
                                        }, array_filter(explode("|", (string)$row['items_data'])))) ?>'
                                    >Details</button>
										
                                   <?php if ($row['status'] === 'approved'): // THIS WILL SHOW COLLECT BUTTON IF THE STATUS IS APPROVED ELSE IT DISABLE THE COLLECT BUTTON
								   ?>
								   
								<button class="btn btn-sm btn-success collectBtn" data-id="<?= $row['donation_id'] ?>">Collect</button>
								<?php else: ?>
								<button class="btn btn-sm btn-secondary" disabled>Collect</button>
							<?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">No approved donations found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <nav>
                <ul class="pagination justify-content-center">
                    <?php
                        $qs_base = "view=history&search=" . urlencode($search) . "&sort={$sort}&order={$order}";
                        for ($i = 1; $i <= $total_pages; $i++):
                    ?>
                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= $qs_base ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>

        <?php elseif ($view === 'messages'): ?>
            <h3 class="mb-4">Messages</h3>
            <div class="chat-container shadow-sm">
                <div class="col-md-4 conversation-list p-3">
                    <button class="chat-action-btn" data-bs-toggle="modal" data-bs-target="#newChatModal">
                        <i class="fas fa-plus me-2"></i> Start New Chat
                    </button>
                    <hr class="text-white-50 my-3">
                    <div class="list-group list-group-flush" id="conversation-list">
                        <p class="text-center p-3 text-muted">Loading conversations...</p>
                    </div>
                </div>
                <div class="col-md-8 chat-window">
                    <div id="chat-welcome" class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <h5>Select a conversation or start a new one</h5>
                    </div>
                    <div id="chat-area" class="d-none w-100 h-100 d-flex flex-column">
                        <div class="chat-header"><h5 id="chat-with-name"></h5></div>
                        <div class="chat-body" id="chat-body"></div>
                        <div class="chat-footer">
                            <form id="message-form">
                                <div class="input-group">
                                    <input type="text" id="message-input" class="form-control" placeholder="Type a message..." required autocomplete="off">
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i> Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Start New Chat Modal -->
            <div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newChatModalLabel">Start a New Chat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="donor-search" placeholder="Search for a donor...">
                        </div>
                        <ul id="donors-list" class="list-group list-group-flush">
                            <?php foreach ($donors_for_messages as $d): ?>
                                <li class="list-group-item list-group-item-action d-flex align-items-center"
                                    data-donor-id="<?= $d['id'] ?>" data-donor-name="<?= htmlspecialchars($d['fullname']) ?>">
                                    <img src="<?= htmlspecialchars($d['profile_picture'] ?? '../uploads/profile_pictures/default-avatar.png') ?>"
                                         class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    <strong><?= htmlspecialchars($d['fullname']) ?></strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div></div>
            </div>
        <?php endif; ?>

        <!-- Details Modal -->
        <div class="modal fade" id="donationDetailsModal" tabindex="-1" aria-labelledby="donationDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="donationDetailsModalLabel">Donation Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Caption:</strong> <span id="modal-caption"></span></p>
                        <hr>
                        <h6>Donation Items:</h6>
                        <ul id="modal-items-list" class="list-group mb-3"></ul>
                        <h6>Donation Conditions:</h6>
                        <ul id="modal-conditions-list" class="list-group mb-3"></ul>
                        <p><strong>Delivery Mode:</strong> <span id="modal-delivery-mode"></span></p>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title" id="imageModalLabel">Donation Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                    <div class="modal-body text-center">
                        <img src="" id="modal-image" alt="Donation Image" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($view === ''): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = <?= json_encode($chartMonths) ?>;
    const postsData = <?= json_encode($chartPosts) ?>;
    const donationsData = <?= json_encode($chartDonations) ?>;

    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Approved Posts', data: postsData, backgroundColor: '#4e73df' },
                { label: 'Collected Donations', data: donationsData, backgroundColor: '#1cc88a' }
            ]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
});
</script>
<?php endif; ?>

<script>

// This helper function manually parses the specific 'YYYY-MM-DD HH:MM:SS' format.
function parseSQLDateTime(dateStr) {
    if (!dateStr || typeof dateStr !== 'string') return null;

    const [datePart, timePart] = dateStr.split(' ');
    if (!datePart || !timePart) return null;

    const [year, month, day] = datePart.split('-').map(Number);
    const [hour, minute, second] = timePart.split(':').map(Number);

    // This is the key: Create the date using components.
    // The 'month - 1' is crucial because JavaScript months are 0-indexed (0=Jan, 1=Feb, etc.).
    if (isNaN(year) || isNaN(month)) return null;
    return new Date(year, month - 1, day, hour, minute, second);
}

function formatDateTime(dateInput) {
    let date;

    if (dateInput instanceof Date) {
        date = dateInput; // It's already a Date object
    } else {
        date = parseSQLDateTime(dateInput); // Use our robust parser for strings
    }

    // Check if the date is valid after parsing
    if (!date || isNaN(date.getTime())) {
        // For your own debugging, see what's causing the error
        console.error("Could not parse date input:", dateInput); 
        return 'Invalid Date';
    }

    const now = new Date();
    const sameDay = date.toDateString() === now.toDateString();
    const sameYear = date.getFullYear() === now.getFullYear();

    if (sameDay) {
        // Format: "10:45 AM"
        return date.toLocaleString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    } else if (sameYear) {
        // Format: "Sep 1"
        return date.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
        });
    } else {
        // Format: "Sep 1, 2024"
        return date.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
        });
    }
}

// Details modal fill (same UX as donor dashboard)
document.addEventListener('DOMContentLoaded', function () {
    const detailsModal = document.getElementById('donationDetailsModal');
    if (detailsModal) {
        detailsModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const deliveryMode = button.getAttribute('data-delivery-mode') || 'N/A';
            const caption = button.getAttribute('data-caption') || 'N/A';
            const itemsJson = button.getAttribute('data-items') || '[]';
            const conditionsJson = button.getAttribute('data-donation-conditions') || '{}';

            let items = [], conditions = {};
            try { items = JSON.parse(itemsJson); } catch(e) {}
            try { conditions = JSON.parse(conditionsJson); } catch(e) {}

            document.getElementById('modal-delivery-mode').textContent = deliveryMode;
            document.getElementById('modal-caption').textContent = caption;

            const itemsList = document.getElementById('modal-items-list');
            itemsList.innerHTML = '';
            if (Array.isArray(items) && items.length) {
                items.forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item';
                    li.innerHTML = `<strong>Item:</strong> ${item.type}<br><strong>Quantity:</strong> ${item.quantity}<br><strong>Expiry Date:</strong> ${item.expiry}`;
                    itemsList.appendChild(li);
                });
            } else {
                itemsList.innerHTML = '<li class="list-group-item text-muted">No items specified.</li>';
            }

            const conditionsList = document.getElementById('modal-conditions-list');
            conditionsList.innerHTML = '';
            Object.entries(conditions).forEach(([key, value]) => {
                const li = document.createElement('li');
                const cleanKey = key.replace(/_/g, ' ');
                const icon = (String(value) === '1') ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
                li.className = 'list-group-item';
                li.innerHTML = `<i class="fas ${icon} me-2"></i><strong>${cleanKey}:</strong> ${(String(value) === '1') ? 'Yes' : 'No'}`;
                conditionsList.appendChild(li);
            });
        });
    }

    // Image modal
    document.querySelectorAll('.image-thumbnail').forEach(img => {
        img.addEventListener('click', function (e) {
            const url = e.target.getAttribute('data-image-url');
            document.getElementById('modal-image').src = url;
        });
    });

    // Collect action (AJAX)
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.collectBtn');
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        if (!id) return;
        if (!confirm('Mark this donation as collected?')) return;

        try {
            const res = await fetch('actions/collect_donation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ donation_id: id })
            });
            const data = await res.json();
            if (data.success) {
                // Since this tab only shows "approved", remove row after collection
                const row = document.getElementById('donation-' + id);
                if (row) row.remove();
            } else {
                alert(data.message || 'Failed to update donation.');
            }
        } catch (err) {
            alert('Network error while updating donation.');
        }
    });

    // Messaging (mirrors donor UX)
    const view = '<?= $view ?>';
    const currentUserId = <?= $org_id ?>;
    if (view === 'messages') {
        const conversationList = document.getElementById('conversation-list');
        const chatWelcome = document.getElementById('chat-welcome');
        const chatArea = document.getElementById('chat-area');
        const chatWithName = document.getElementById('chat-with-name');
        const chatBody = document.getElementById('chat-body');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');

        const donorsList = document.getElementById('donors-list');
        const donorSearch = document.getElementById('donor-search');
        const newChatModal = new bootstrap.Modal(document.getElementById('newChatModal'));

        let activeRecipientId = null;
        let messageInterval = null;

        async function loadConversations() {
            try {
                const response = await fetch('actions/get_conversations.php');
                const conversations = await response.json();
                conversationList.innerHTML = '';
                if (!conversations.length) {
                    conversationList.innerHTML = '<p class="text-center p-3 text-muted">No conversations yet.</p>';
                    return;
                }
                conversations.forEach(convo => {
                    const a = document.createElement('a');
                    a.className = 'list-group-item list-group-item-action d-flex align-items-center';
                    a.href = '#';
                    a.dataset.recipientId = convo.id;
                    a.dataset.recipientName = convo.fullname;
                    const pic = convo.profile_picture ? `../uploads/${convo.profile_picture}` : '../uploads/profile_pictures/default-avatar.png';
                    a.innerHTML = `<img src="${pic}" class="rounded-circle me-3" style="width:50px;height:50px;object-fit:cover;"><strong>${convo.fullname}</strong>`;
                    a.addEventListener('click', (ev) => {
                        ev.preventDefault();
                        document.querySelectorAll('#conversation-list a').forEach(el => el.classList.remove('active'));
                        a.classList.add('active');
                        activeRecipientId = convo.id;
                        chatWithName.textContent = convo.fullname;
                        chatWelcome.classList.add('d-none');
                        chatArea.classList.remove('d-none');
                        loadMessages(activeRecipientId);
                    });
                    conversationList.appendChild(a);
                });
            } catch {
                conversationList.innerHTML = '<p class="text-center p-3 text-danger">Could not load conversations.</p>';
            }
        }

   async function loadMessages(recipientId, scroll = true) {
    if (!recipientId) return;
    
    // The auto-refresh interval logic has been completely removed.

    try {
        const response = await fetch(`actions/get_messages.php?recipient_id=${recipientId}`);
        const messages = await response.json();
        
        chatBody.innerHTML = ''; // Clear the chat window before adding new messages
        
        messages.forEach(msg => {
            const div = document.createElement('div');
            // This logic correctly determines if the message was sent or received
            div.className = 'message ' + (msg.sender_id == currentUserId ? 'sent' : 'received');

            const time = formatDateTime(msg.created_at);

            div.innerHTML = `
                <div>${msg.message_content}</div>
                <div class="small text-muted mt-1" style="font-size: 0.75rem;">${time}</div>
            `;
            // Adds the message to the bottom of the chat
            chatBody.appendChild(div);
        });

        // Scrolls to the bottom of the chat if it's the first load or after sending a message
        if (scroll) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
        
    } catch (error) {
        console.error("Failed to load messages:", error);
    }
}

// The old renderMessage function is no longer needed and can be removed.

messageForm.addEventListener('submit', async function (e) {
    e.preventDefault();
    const text = messageInput.value.trim();
    if (!text || !activeRecipientId) return;

    // Clear the input right away for a responsive feel
    messageInput.value = '';

    try {
        const response = await fetch('actions/send_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ recipient_id: activeRecipientId, message: text })
        });
        
        const result = await response.json();
        
        // After the message is confirmed sent by the server, reload the chat
        if (result.success) {
            await loadMessages(activeRecipientId, true); // Reload and scroll to the new message
        } else {
            console.error(result.message || 'Failed to send message');
        }
    } catch (err) {
        console.error('Network error while sending message:', err);
    }
});
        donorsList.addEventListener('click', function (e) {
            const li = e.target.closest('li.list-group-item');
            if (!li) return;
            activeRecipientId = li.dataset.donorId;
            chatWithName.textContent = li.dataset.donorName;
            chatWelcome.classList.add('d-none');
            chatArea.classList.remove('d-none');
            loadMessages(activeRecipientId);
            newChatModal.hide();
            document.querySelectorAll('#conversation-list a').forEach(el => el.classList.remove('active'));
        });

        donorSearch.addEventListener('input', function (e) {
            const q = e.target.value.toLowerCase();
            document.querySelectorAll('#donors-list li').forEach(li => {
                const name = li.dataset.donorName.toLowerCase();
                li.style.display = name.includes(q) ? 'flex' : 'none';
            });
        });

        loadConversations();
    }
});
</script>

</body>
</html>
