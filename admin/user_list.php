<?php
include '../includes/db.php';

$type = $_GET['type'] ?? 'donors';
$search = "%" . ($_GET['search'] ?? '') . "%";
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

$role = ($type === 'donors') ? 'donor' : 'organization';

// Count total
$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE role=? AND (fullname LIKE ? OR email LIKE ? OR contact LIKE ?)");
$stmt->bind_param("ssss", $role, $search, $search, $search);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

$totalPages = ceil($total / $limit);

// Fetch rows
$sql = "SELECT id, fullname, email, address, contact, birthday, valid_id, selfie 
        FROM users 
        WHERE role=? AND (fullname LIKE ? OR email LIKE ? OR contact LIKE ?)
        ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii", $role, $search, $search, $search, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

?>

<table class="table table-bordered table-hover">
  <thead class="table-light">
    <tr>
      <th>Full Name</th>
      <th>Email</th>
      <th>Address</th>
      <th>Contact</th>
      <th>Birthday</th>
      <th>Valid ID</th>
      <th>Selfie</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($result->num_rows): ?>
      <?php while($row=$result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['fullname']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['address']) ?></td>
          <td><?= htmlspecialchars($row['contact']) ?></td>
          <td><?= htmlspecialchars($row['birthday']) ?></td>
          <td>
            <?php if($row['valid_id']): ?>
              <img src="../uploads/ids/<?= htmlspecialchars($row['valid_id']) ?>" class="thumb-img" alt="Valid ID" onclick="parent.previewImage(this.src);">
            <?php else: ?>
              N/A
            <?php endif; ?>
          </td>
          <td>
            <?php if($row['selfie']): ?>
              <img src="../uploads/selfies/<?= htmlspecialchars($row['selfie']) ?>" class="thumb-img" alt="Selfie" onclick="parent.previewImage(this.src);">
            <?php else: ?>
              N/A
            <?php endif; ?>
          </td>
          <td>
            <button class="btn btn-sm btn-warning edit-btn" onclick="editUser(<?= $row['id'] ?>)">Edit</button>
            <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $row['id'] ?>,'<?= htmlspecialchars($row['fullname']) ?>')">Delete</button>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="8" class="text-center text-muted">No users found</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php if($totalPages > 1): ?>
<nav>
  <ul class="pagination justify-content-center">
    <?php for($i=1;$i<=$totalPages;$i++): ?>
      <li class="page-item <?= $i==$page ? 'active' : '' ?>">
        <a class="page-link" href="javascript:void(0)" onclick="loadDonors(<?= $i ?>)"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>
