<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php"); exit();
}

// — Metrics Counts
$counts = [
  'admins' => $conn->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetch_row()[0],
  'donors' => $conn->query("SELECT COUNT(*) FROM users WHERE role='donor'")->fetch_row()[0],
  'orgs' => $conn->query("SELECT COUNT(*) FROM users WHERE role='organization'")->fetch_row()[0],
  'pending_accounts' => $conn->query("SELECT COUNT(*) FROM users WHERE status='pending'")->fetch_row()[0],
  'donation_requests' => $conn->query("SELECT COUNT(*) FROM donations WHERE status='pending'")->fetch_row()[0],
  'donation_collected' => $conn->query("SELECT COUNT(*) FROM donations WHERE status='collected'")->fetch_row()[0]
];

// — Admin Notifications & Unread Count
$notifs = $conn->query("SELECT id, message, created_at, is_read FROM admin_notifications ORDER BY created_at DESC LIMIT 5");
$unreadCount = $conn->query("SELECT COUNT(*) FROM admin_notifications WHERE is_read=0")->fetch_row()[0];

// — Monthly Stats Last 6 Months
$stats = $conn->query("
  SELECT DATE_FORMAT(created_at,'%Y-%m') AS month,
    COUNT(*) AS posts_approved,
    (SELECT COUNT(*) FROM donations d2 WHERE d2.status='collected' AND DATE_FORMAT(d2.created_at,'%Y-%m')=DATE_FORMAT(p.created_at,'%Y-%m')) AS donations_collected
  FROM posts p WHERE p.status='approved'
  GROUP BY month ORDER BY month DESC LIMIT 6
");

// — Top 5 Active Donors (most donations)
$topDonors = $conn->query("
  SELECT u.fullname, COUNT(d.id) AS total
  FROM users u
  JOIN donations d ON d.donor_id = u.id AND d.status = 'collected'
  GROUP BY u.id ORDER BY total DESC LIMIT 5
");
?>
<?php
// — Top 5 Active Organizations (most accepted donations)
$topOrgs = $conn->query("
  SELECT u.fullname, COUNT(d.id) AS total
  FROM users u
  JOIN donations d ON d.organization_id = u.id AND d.status = 'accepted'
  GROUP BY u.id ORDER BY total DESC LIMIT 5
");

?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>.bell { font-size:1.6rem; position:relative; cursor:pointer; }.badge-dot { position:absolute; top:0; right:0; }</style>
</head>
<body class="container py-4">

<div class="d-flex justify-content-between align-items-center">
  <h2>Admin Dashboard</h2>
  <div class="dropdown">
    <span id="bellIcon" class="bell" data-bs-toggle="dropdown">🔔
      <?php if($unreadCount): ?><span class="badge bg-danger rounded-pill badge-dot" id="notifBadge"><?= $unreadCount ?></span><?php endif; ?>
    </span>
    <ul class="dropdown-menu dropdown-menu-end" id="notifList" style="width:300px">
      <?php while($n = $notifs->fetch_assoc()): ?>
        <li class="dropdown-item<?= !$n['is_read'] ? ' fw-bold' : '' ?>">
          <?= htmlspecialchars($n['message']) ?><br>
          <small class="text-muted"><?= date('M j, g:i a', strtotime($n['created_at'])) ?></small>
        </li>
      <?php endwhile; ?>
      <?php if(!$notifs->num_rows): ?><li class="dropdown-item text-center text-muted">No notifications</li><?php endif; ?>
    </ul>
  </div>
  <a href="../logout.php" class="btn btn-outline-secondary">Logout</a>
</div>

<hr>

<div class="row g-3 mb-4">
  <?php foreach ($counts as $key => $val): ?>
    <div class="col-md-3">
      <div class="card border-primary">
        <div class="card-body">
          <h5 class="card-title"><?= ucfirst(str_replace('_',' ', $key)) ?></h5>
          <p class="fs-4"><?= $val ?></p>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row mb-4">
  <div class="col-md-8">
    <h4>Monthly Stats</h4>
    <canvas id="monthlyChart"></canvas>
    <button class="btn btn-sm btn-primary mt-2" onclick="exportData()">Export CSV</button>
  </div>
  <div class="col-md-4">
  
    <h4>Top 5 Most Donors</h4>
    <ul class="list-group">
      <?php while ($d = $topDonors->fetch_assoc()): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <?= htmlspecialchars($d['fullname']) ?>
          <span class="badge bg-success"><?= $d['total'] ?></span>
        </li>
      <?php endwhile; ?>
      <?php if (!$topDonors->num_rows): ?><li class="list-group-item text-muted">No active donors</li><?php endif; ?>
	  <div class="col-md-4">
  <h4>Top 5 Active Organizations</h4>
  <ul class="list-group">
    <?php while ($org = $topOrgs->fetch_assoc()): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <?= htmlspecialchars($org['fullname']) ?>
        <span class="badge bg-primary"><?= $org['total'] ?></span>
      </li>
    <?php endwhile; ?>
    <?php if (!$topOrgs->num_rows): ?>
      <li class="list-group-item text-muted">No active organizations</li>
    <?php endif; ?>
  </ul>
</div>

    </ul>
  </div>
  
</div>

<hr>

<h4>Admin Tools</h4>
<ul>
  <li><a href="approve_accounts.php">✅ Approve/Deny User Accounts</a></li>
  <li><a href="admin_posts.php">📝 Approve/Deny Community Posts</a></li>
  <li><a href="approve_donations.php">📦 Approve/Deny Donations</a></li>
  <li><a href="audit_trail.php">🔍 View Audit Trail</a></li>
</ul>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Parse Chart data
const months = [], postsData = [], donationsData = [];
<?php while($row = $stats->fetch_assoc()): ?>
  months.unshift("<?= $row['month'] ?>");
  postsData.unshift(<?= $row['posts_approved'] ?>);
  donationsData.unshift(<?= $row['donations_collected'] ?>);
<?php endwhile; ?>

new Chart(document.getElementById('monthlyChart'), {
  type: 'bar',
  data: { labels: months, datasets: [
    { label: 'Approved Posts', data: postsData, backgroundColor: '#4e73df' },
    { label: 'Donations Collected', data: donationsData, backgroundColor: '#1cc88a' }
  ]},
  options: { responsive: true, plugins: { tooltip: { mode: 'index', intersect: false }},
             scales: { y: { beginAtZero: true } } }
});

// CSV Export
function exportData() {
  const csv = ['Month,Posts,Donations'];
  for(let i=0;i<months.length;i++) {
    csv.push(`${months[i]},${postsData[i]},${donationsData[i]}`);
  }
  const blob = new Blob([csv.join('\n')], {type:'text/csv'});
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'monthly_stats.csv';
  link.click();
}

// Auto-refresh notifications every 30s
function refreshNotifications() {
  fetch('../handlers/fetch_admin_notifs.php')
    .then(r => r.json())
    .then(data => {
      const list = document.getElementById('notifList');
      const badge = document.getElementById('notifBadge');
      list.innerHTML = '';
      let newHtml = '';
      data.forEach(n => {
        newHtml += `<li class="dropdown-item${n.is_read ? '' : ' fw-bold'}">
          ${n.message}<br><small class="text-muted">${n.when}</small></li>`;
      });
      list.innerHTML = newHtml || '<li class="dropdown-item text-muted">No notifications</li>';
      if (badge) badge.textContent = data.filter(n => !n.is_read).length;
    });
}
setInterval(refreshNotifications, 30000);
</script>
</body>
</html>
