<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'organization') {
    header("Location: ../login.php");
    exit();
}

$org_id = $_SESSION['user']['id'];

// Handle Collect action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['collect_id'])) {
    $donation_id = intval($_POST['collect_id']);

    // Mark as collected
    $update = $conn->prepare("UPDATE donations SET status = 'collected' WHERE id = ? AND organization_id = ?");
    $update->bind_param("ii", $donation_id, $org_id);
    $update->execute();

    // Get donor ID
    $getDonor = $conn->prepare("SELECT donor_id FROM donations WHERE id = ?");
    $getDonor->bind_param("i", $donation_id);
    $getDonor->execute();
    $donor = $getDonor->get_result()->fetch_assoc();

    if ($donor) {
        $donor_id = $donor['donor_id'];
        $msg = "Your donation ID #$donation_id has been collected.";
        $notify = $conn->prepare("INSERT INTO notifications (donor_id, donation_id, message) VALUES (?, ?, ?)");
        $notify->bind_param("iis", $donor_id, $donation_id, $msg);
        $notify->execute();
    }

    header("Location: dashboard.php?view=history");
    exit();
}

$view = $_GET['view'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Organization Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <h2 class="mb-3">Welcome, <?= htmlspecialchars($_SESSION['user']['fullname']) ?>!</h2>

        <nav class="mb-4">
            <a href="dashboard.php" class="btn btn-outline-primary btn-sm">Dashboard</a>
			<a href="../shared/community_posts.php">Community Posts</a>
            <a href="dashboard.php?view=history" class="btn btn-outline-secondary btn-sm">Donations & History</a>
            <a href="profile.php" class="btn btn-outline-success btn-sm">Profile</a>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </nav>

        <?php if ($view === 'history'): ?>
            <h4>Donation History</h4>
            <?php
            $stmt = $conn->prepare("
                SELECT d.id AS donation_id, d.created_at, d.delivery_mode, d.status,
                       u.fullname AS donor_name, u.address, u.contact,
                       GROUP_CONCAT(CONCAT(i.item_type, ' (', i.quantity, ')') SEPARATOR ', ') AS items,
                       GROUP_CONCAT(di.image_path) AS images
                FROM donations d
                LEFT JOIN donation_items i ON d.id = i.donation_id
                LEFT JOIN donation_images di ON d.id = di.donation_id
                INNER JOIN users u ON u.id = d.donor_id
                WHERE d.organization_id = ? AND d.status != 'pending'
                GROUP BY d.id
                ORDER BY d.created_at DESC
            ");
            $stmt->bind_param("i", $org_id);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Photos</th>
                        <th>Delivery</th>
                        <th>Donor Address</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['donation_id'] ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td><?= htmlspecialchars($row['items']) ?></td>
                        <td>
                            <?php
                            if ($row['images']) {
                                foreach (explode(",", $row['images']) as $img) {
                                    echo "<a href='../uploads/{$img}' target='_blank'><img src='../uploads/{$img}' width='40'></a> ";
                                }
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row['delivery_mode']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['contact']) ?></td>
                        <td><span class="badge bg-<?= 
                            $row['status'] === 'collected' ? 'success' :
                            ($row['status'] === 'approved' ? 'primary' :
                            ($row['status'] === 'rejected' ? 'danger' : 'secondary')) ?>">
                            <?= ucfirst($row['status']) ?>
                        </span></td>
                        <td>
                            <?php if ($row['status'] === 'approved'): ?>
                                <form method="post">
                                    <input type="hidden" name="collect_id" value="<?= $row['donation_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success">Collect</button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-sm btn-secondary" disabled>Collect</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
				
            </table>
			<!-- Chart Filter & Container -->
<form method="get" class="row g-2 mt-4 mb-3">
    <input type="hidden" name="view" value="history">
    <div class="col-auto">
        <label for="start_date" class="form-label">From</label>
        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= $_GET['start_date'] ?? '' ?>">
    </div>
    <div class="col-auto">
        <label for="end_date" class="form-label">To</label>
        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= $_GET['end_date'] ?? '' ?>">
    </div>
    <div class="col-auto">
        <label for="chart_type" class="form-label">Chart Type</label>
        <select name="chart_type" id="chart_type" class="form-select">
            <option value="pie" <?= ($_GET['chart_type'] ?? '') === 'pie' ? 'selected' : '' ?>>Pie</option>
            <option value="bar" <?= ($_GET['chart_type'] ?? '') === 'bar' ? 'selected' : '' ?>>Bar</option>
        </select>
    </div>
    <div class="col-auto d-flex align-items-end">
        <button type="submit" class="btn btn-primary">Update Chart</button>
    </div>
</form>

<?php
$start = $_GET['start_date'] ?? null;
$end = $_GET['end_date'] ?? null;
$chart_type = $_GET['chart_type'] ?? 'pie';

$colors = [
    'approved' => '#0d6efd',
    'collected' => '#198754',
    'rejected' => '#dc3545'
];

$chart_sql = "SELECT status, COUNT(*) AS count FROM donations WHERE organization_id = ? AND status != 'pending'";
$types = "i";
$params = [$org_id];

if ($start) {
    $chart_sql .= " AND created_at >= ?";
    $types .= "s";
    $params[] = $start;
}
if ($end) {
    $chart_sql .= " AND created_at <= ?";
    $types .= "s";
    $params[] = $end;
}

$chart_sql .= " GROUP BY status";
$stmtChart = $conn->prepare($chart_sql);
$stmtChart->bind_param($types, ...$params);
$stmtChart->execute();
$chartResult = $stmtChart->get_result();

$labels = [];
$counts = [];
$backgrounds = [];

while ($row = $chartResult->fetch_assoc()) {
    $labels[] = ucfirst($row['status']) . " ({$row['count']})";
    $counts[] = $row['count'];
    $backgrounds[] = $colors[$row['status']] ?? '#6c757d';
}
?>

<div class="chart-container mb-4" style="max-width: 500px; margin: auto;">
    <canvas id="statusChart" height="300"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById("statusChart").getContext("2d");
    new Chart(ctx, {
        type: "<?= $chart_type ?>",
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                data: <?= json_encode($counts) ?>,
                backgroundColor: <?= json_encode($backgrounds) ?>
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: context => `${context.label}`
                    }
                }
            }
        }
    });
</script>

        <?php else: ?>
            <h4>Dashboard Overview</h4>
            <p class="text-muted">Use the menu above to view donation history, collect items, or update your profile.</p>
        <?php endif; ?>
		
   
    </div>
	
</body>
</html>
