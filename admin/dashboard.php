<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../logout.php");
    exit();
}

// Quick counts
$counts = [
    'admins' => $conn->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetch_row()[0],
    'donors' => $conn->query("SELECT COUNT(*) FROM users WHERE role='donor'")->fetch_row()[0],
    'orgs' => $conn->query("SELECT COUNT(*) FROM users WHERE role='organization'")->fetch_row()[0],
    'pending_accounts' => $conn->query("SELECT COUNT(*) FROM users WHERE status='pending'")->fetch_row()[0],
    'pending_posts' => $conn->query("SELECT COUNT(*) FROM posts WHERE status='pending'")->fetch_row()[0],
    'donation_requests' => $conn->query("SELECT COUNT(*) FROM donations WHERE status='pending'")->fetch_row()[0],
    'donation_collected' => $conn->query("SELECT COUNT(*) FROM donations WHERE status='collected'")->fetch_row()[0]
];

// Monthly stats (last 6 months)
$stats = $conn->query("
    SELECT DATE_FORMAT(created_at,'%Y-%m') AS month,
    COUNT(*) AS posts_approved,
    (SELECT COUNT(*) FROM donations d2 WHERE d2.status='collected' AND DATE_FORMAT(d2.created_at,'%Y-%m')=DATE_FORMAT(p.created_at,'%Y-%m')) AS donations_collected
    FROM posts p WHERE p.status='approved'
    GROUP BY month ORDER BY month DESC LIMIT 6
");

// Top donors - filtered for the current year
$topDonors = $conn->query("
    SELECT u.fullname, COUNT(d.id) AS total
    FROM users u
    JOIN donations d ON d.donor_id = u.id
    WHERE d.status = 'collected' AND YEAR(d.created_at) = YEAR(CURDATE())
    GROUP BY u.id
    ORDER BY total DESC
    LIMIT 5
");

// Top orgs - filtered for the current year
$topOrgs = $conn->query("
    SELECT u.fullname, COUNT(d.id) AS total
    FROM users u
    JOIN donations d ON d.organization_id = u.id
    WHERE d.status = 'collected' AND YEAR(d.created_at) = YEAR(CURDATE())
    GROUP BY u.id
    ORDER BY total DESC
    LIMIT 5
");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="admindashboard.css"/>
</head>
<body>

    <header class="navbar-header">
        <div class="navbar-content">
            <div class="logo-section">
                <img src="images/logo.png" alt="Food Bridge Logo" class="navbar-logo">
            </div>
        </div>
    </header>

    <div class="row g-0">
        <div class="col-md-2 sidebar">
            <h4 class="text-white mb-4">Admin Panel</h4>
            <a href="#" class="active">📊 Dashboard</a>
            <a href="approve_donations.php">📦 Donations</a>
            <div class="dropdown">
                <a class="dropdown-toggle text-white d-block py-2 px-2" href="#" id="manageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    👥 Manage Users
                </a>
                <ul class="dropdown-menu" aria-labelledby="manageDropdown">
                    <li><a class="dropdown-item" href="manage_donors.php">Donors</a></li>
                    <li><a class="dropdown-item" href="manage_orgs.php">Organizations</a></li>
                    <li><a class="dropdown-item" href="manage_admin.php">Admins</a></li>
                    <li><a class="dropdown-item" href="add_account.php">Add Account</a></li>
                </ul>
            </div>
            <a href="approve_accounts.php">✅ Approve Accounts</a>
            <a href="admin_posts.php">📝 Approve Posts</a>
            <a href="audit_trail.php">🔍 Audit Trail</a>
            <a href="../logout.php">🚪 Logout</a>
        </div>

        <div class="col-md-10 content">
    <div class="content-header">
        <h2>Admin Dashboard</h2>
        <hr>
    </div>

    <div class="dashboard-section">
        <div class="row g-3 mb-4">
            <?php $i = 1; foreach ($counts as $k => $v) : ?>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm card-metric card-metric-<?= $i ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= ucfirst(str_replace('_', ' ', $k)) ?></h5>
                            <p class="display-6"><?= $v ?></p>
                        </div>
                    </div>
                </div>
            <?php $i++; endforeach; ?>
        </div>
    </div>
    
    <div class="dashboard-section">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="box-wrapper">
                    <h4 class="section-title">Monthly Stats</h4>
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="box-wrapper">
                            <h4 class="section-title">Top Donors (This Year)</h4>
                            <ul class="list-group">
                                <?php while ($d = $topDonors->fetch_assoc()) : ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><?= htmlspecialchars($d['fullname']) ?></span>
                                        <span class="badge bg-success rounded-pill"><?= $d['total'] ?></span>
                                    </li>
                                <?php endwhile; ?>
                                <?php if (!$topDonors->num_rows) : ?><li class="list-group-item text-muted">No donors found this year.</li><?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="box-wrapper">
                            <h4 class="section-title">Top Organizations (This Year)</h4>
                            <ul class="list-group">
                                <?php while ($o = $topOrgs->fetch_assoc()) : ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><?= htmlspecialchars($o['fullname']) ?></span>
                                        <span class="badge bg-primary rounded-pill"><?= $o['total'] ?></span>
                                    </li>
                                <?php endwhile; ?>
                                <?php if (!$topOrgs->num_rows) : ?><li class="list-group-item text-muted">No organizations found this year.</li><?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const months = [],
            postsData = [],
            donationsData = [];
        <?php while ($row = $stats->fetch_assoc()) : ?>
            months.unshift("<?= $row['month'] ?>");
            postsData.unshift(<?= $row['posts_approved'] ?>);
            donationsData.unshift(<?= $row['donations_collected'] ?>);
        <?php endwhile; ?>

        new Chart(document.getElementById('monthlyChart'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Approved Posts',
                    data: postsData,
                    backgroundColor: '#4e73df'
                }, {
                    label: 'Donations Collected',
                    data: donationsData,
                    backgroundColor: '#1cc88a'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        function loadUsers(type, page = 1) {
            console.log(`Loading ${type} on page ${page}`);
        }

        function editUser(id) {
            console.log('Editing user:', id);
        }

        function deleteUser(id, name) {
            console.log('Deleting user:', id);
        }

        function scrollToUsers() {
            console.log('Scrolling to users section.');
        }
    </script>
</body>
</html>