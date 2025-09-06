<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit("Unauthorized");
}

$type = $_GET['type'] ?? 'pending';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

if ($type === 'pending') {
    $sql = "SELECT d.id, d.caption, d.status, u.fullname AS donor, d.created_at
            FROM donations d
            JOIN users u ON d.donor_id=u.id
            WHERE d.status='pending'
            ORDER BY d.created_at DESC";
} else {
    $sql = "SELECT d.id, d.caption, d.status, u.fullname AS donor, d.created_at
            FROM donations d
            JOIN users u ON d.donor_id=u.id
            WHERE d.status IN ('approved','rejected','collected')
            ORDER BY d.created_at DESC
            LIMIT $limit OFFSET $offset";
}
$res = $conn->query($sql);

if ($type === 'history') {
    $total = $conn->query("SELECT COUNT(*) FROM donations WHERE status IN ('approved','rejected','collected')")->fetch_row()[0];
    $pages = ceil($total / $limit);
}

echo "<table class='table table-bordered'>";
echo "<tr><th>ID</th><th>Donor</th><th>Caption</th><th>Status</th><th>Date</th><th>Actions</th></tr>";
while($row=$res->fetch_assoc()){
    echo "<tr data-id='{$row['id']}'>
      <td>{$row['id']}</td>
      <td>".htmlspecialchars($row['donor'])."</td>
      <td>".htmlspecialchars($row['caption'])."</td>
      <td><span class='badge bg-secondary'>{$row['status']}</span></td>
      <td>".date('M j, Y g:i a', strtotime($row['created_at']))."</td>
      <td>";
    if ($type==='pending') {
        echo "<button class='btn btn-success btn-sm' onclick=\"updateDonation({$row['id']},'approve')\">Approve</button>
              <button class='btn btn-danger btn-sm' onclick=\"updateDonation({$row['id']},'reject')\">Reject</button>";
    } else {
        echo "<button class='btn btn-warning btn-sm' onclick=\"updateDonation({$row['id']},'undo')\">Undo</button>";
    }
    echo "</td></tr>";
}
echo "</table>";

if ($type==='history' && $pages>1) {
    echo "<nav><ul class='pagination'>";
    for ($i=1;$i<=$pages;$i++) {
        $active = ($i==$page) ? 'active' : '';
        echo "<li class='page-item $active'><a class='page-link' href='#' onclick=\"loadDonations('history',$i)\">$i</a></li>";
    }
    echo "</ul></nav>";
}
