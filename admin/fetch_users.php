<?php
include '../includes/db.php';

$role = $_GET['role'] ?? 'donors';
$page = max(1, intval($_GET['page'] ?? 1));
$q = $conn->real_escape_string($_GET['q'] ?? '');
$limit = 5;
$offset = ($page-1)*$limit;

$roleVal = $role=='donors' ? 'donor' : 'organization';

$where = "role='$roleVal'";
if ($q) $where .= " AND (fullname LIKE '%$q%' OR email LIKE '%$q%' OR address LIKE '%$q%' OR contact LIKE '%$q%')";

$total = $conn->query("SELECT COUNT(*) FROM users WHERE $where")->fetch_row()[0];
$res = $conn->query("SELECT id, fullname,email,address,contact FROM users WHERE $where ORDER BY fullname LIMIT $limit OFFSET $offset");

echo '<table class="table table-bordered table-striped">';
echo '<tr><th>Full Name</th><th>Email</th><th>Address</th><th>Contact</th><th>Actions</th></tr>';
while($row=$res->fetch_assoc()){
  $id = $row['id'];
  echo '<tr>';
  echo '<td>'.htmlspecialchars($row['fullname']).'</td>';
  echo '<td>'.htmlspecialchars($row['email']).'</td>';
  echo '<td>'.htmlspecialchars($row['address']).'</td>';
  echo '<td>'.htmlspecialchars($row['contact']).'</td>';
  echo '<td>
          <button class="btn btn-sm btn-warning" onclick="editUser('.$id.')">Edit</button>
          <button class="btn btn-sm btn-danger" onclick="deleteUser('.$id.', \''.$role.'\')">Delete</button>
        </td>';
  echo '</tr>';
}
if (!$res->num_rows) echo '<tr><td colspan="5" class="text-center text-muted">No results</td></tr>';
echo '</table>';

// Pagination
$pages = ceil($total/$limit);
if($pages>1){
  echo '<nav><ul class="pagination">';
  for($i=1;$i<=$pages;$i++){
    $active = $i==$page ? 'active' : '';
    echo "<li class='page-item $active'><a class='page-link' href='#' onclick=\"loadUsers('$role',$i,'$q')\">$i</a></li>";
  }
  echo '</ul></nav>';
}
