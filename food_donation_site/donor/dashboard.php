<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'donor') {
    header("Location: ../login.php");
    exit();
}

$donor_id = $_SESSION['user']['id'];
$view = $_GET['view'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'created_at';
$order = $_GET['order'] ?? 'DESC';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Donor Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/donor_dashboard.css">
     <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrNj/2P9Vf4lB1c7l3s+Fv7/J8qLz9x+T+Gq0t1f2FmG8f/n2k+y+L+9qG6d+h+J+A+c+c+u+B+S+q+F+w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
</head>
<body>

<h2>Welcome, <?= htmlspecialchars($_SESSION['user']['fullname']) ?>!</h2>

<nav>
   <nav>
  <nav class="sidebar-nav">
    <ul>
    <li><a href="dashboard.php"class="btn btn-outline-primary btn-sm">Dashboard</a></li>
	<li><a href="../shared/community_posts.php">Community Posts</a></li>
	<li><a href="create_post.php"class="btn btn-outline-primary btn-sm">Create Post</a></li>
	<li><a href="create_donation.php">Post a Donation</a></li>
    <li><a href="dashboard.php?view=history"class="btn btn-outline-secondary btn-sm">History</a></li>
    <li><a href="notifications.php"class="btn btn-outline-success btn-sm">Notifications</a></li>
    <li><a href="profile.php"class="btn btn-outline-success btn-sm">Profile</a></li>
    <li><a href="../logout.php"class="btn btn-outline-success btn-sm">Logout</a></li>
    </ul>
</nav>

</nav>

</nav>

<?php if ($view === 'history'): ?>
    <h3>Donation History</h3>
    <form method="get" action="">
        <input type="hidden" name="view" value="history">
        <input type="text" name="search" placeholder="Search by item or status" value="<?= htmlspecialchars($search) ?>">
        <select name="sort">
            <option value="created_at" <?= $sort == 'created_at' ? 'selected' : '' ?>>Date</option>
            <option value="status" <?= $sort == 'status' ? 'selected' : '' ?>>Status</option>
        </select>
        <select name="order">
            <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Descending</option>
            <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Ascending</option>
        </select>
        <button type="submit">Apply</button>
    </form>
    <?php
        $query = "
            SELECT SQL_CALC_FOUND_ROWS d.id AS donation_id, d.created_at, d.delivery_mode, d.status,
                   GROUP_CONCAT(CONCAT(i.item_type, ' (', i.quantity, ')') SEPARATOR ', ') AS items,
                   GROUP_CONCAT(di.image_path) AS images
            FROM donations d
            LEFT JOIN donation_items i ON d.id = i.donation_id
            LEFT JOIN donation_images di ON d.id = di.donation_id
            WHERE d.donor_id = ?
        ";
        if ($search) {
            $query .= " AND (i.item_type LIKE ? OR d.status LIKE ?)";
        }
        $query .= " GROUP BY d.id ORDER BY $sort $order LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($query);
        if ($search) {
            $like = "%$search%";
            $stmt->bind_param("issii", $donor_id, $like, $like, $limit, $offset);
        } else {
            $stmt->bind_param("iii", $donor_id, $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $total_result = $conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc()['total'];
        $total_pages = ceil($total_result / $limit);
    ?>
    <table>
        <tr>
            <th>Donation ID</th>
            <th>Date</th>
            <th>Items & Quantities</th>
            <th>Images</th>
            <th>Delivery Mode</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['donation_id'] ?></td>
                <td><?= $row['created_at'] ?></td>
                <td><?= htmlspecialchars($row['items']) ?></td>
                <td>
                    <?php
                    if ($row['images']) {
                        $imgs = explode(",", $row['images']);
                        foreach ($imgs as $img) {
                            echo "<a href='../uploads/{$img}' target='_blank'><img src='../uploads/{$img}' alt='Photo' width='40'></a> ";
                        }
                    }
                    ?>
                </td>
                <td><?= $row['delivery_mode'] ?></td>
                <td><?= $row['status'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?view=history&page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
<?php else: ?>
    <h3>Your Donation Overview</h3>
    <form method="get" action="">
        <input type="hidden" name="view" value="">
        <label>From: <input type="date" name="start_date" value="<?= $_GET['start_date'] ?? '' ?>"></label>
        <label>To: <input type="date" name="end_date" value="<?= $_GET['end_date'] ?? '' ?>"></label>
        <select name="chart_type">
            <option value="pie" <?= ($_GET['chart_type'] ?? '') === 'pie' ? 'selected' : '' ?>>Pie</option>
            <option value="bar" <?= ($_GET['chart_type'] ?? '') === 'bar' ? 'selected' : '' ?>>Bar</option>
        </select>
        <button type="submit">Update Chart</button>
    </form>

    <?php
    $start_date = $_GET['start_date'] ?? null;
    $end_date = $_GET['end_date'] ?? null;
    $chart_type = $_GET['chart_type'] ?? 'pie';

    $sql = "SELECT status, COUNT(*) AS count FROM donations WHERE donor_id = ?";
    $params = [$donor_id];
    $types = "i";

    if ($start_date) {
        $sql .= " AND created_at >= ?";
        $params[] = $start_date;
        $types .= "s";
    }
    if ($end_date) {
        $sql .= " AND created_at <= ?";
        $params[] = $end_date;
        $types .= "s";
    }

    $sql .= " GROUP BY status";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels = [];
    $counts = [];
    $colors = ['approved' => '#28a745', 'collected' => '#007bff', 'rejected' => '#dc3545', 'pending' => '#ffc107'];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['status'];
        $counts[] = $row['count'];
    }
    ?>
    <div class="chart-container">
        <canvas id="donationChart" width="400" height="400"></canvas>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('donationChart').getContext('2d');
        new Chart(ctx, {
            type: "<?= $chart_type ?>",
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: <?= json_encode($counts) ?>,
                    backgroundColor: <?= json_encode(array_map(fn($label) => $colors[$label] ?? '#6c757d', $labels)) ?>
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed}`;
                            }
                        }
                    }
                }
            }
        });
    </script>
<?php endif; ?>
<?php
include '../includes/db.php';
$approvedPosts = $conn->query("
    SELECT p.*, u.fullname 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.status = 'approved' 
    ORDER BY p.created_at DESC 
    LIMIT 5
");
?>



</body>

</html>
