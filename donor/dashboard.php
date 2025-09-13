<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'donor') {
    header("Location: ../login.php");
    exit();
}

$donor_id = $_SESSION['user']['id'];
$view = $_GET['view'] ?? '';

// Fetch donor details
$donor_info = [];
$donor_query = $conn->prepare("SELECT fullname, profile_picture FROM users WHERE id = ?");
$donor_query->bind_param("i", $donor_id);
$donor_query->execute();
$donor_result = $donor_query->get_result();
if ($donor_result->num_rows > 0) {
    $donor_info = $donor_result->fetch_assoc();
}
$donor_query->close();

// Fetch dashboard stats only if on the main dashboard view
$counts = [];
$stats = null;
$topItems = null;
$topOrgs = null;
$topDonors = null;
if ($view === '') {
    $counts = [
        'approved_donations' => $conn->query("SELECT COUNT(*) FROM donations WHERE donor_id = $donor_id AND status = 'approved'")->fetch_row()[0],
        'pending_donations' => $conn->query("SELECT COUNT(*) FROM donations WHERE donor_id = $donor_id AND status = 'pending'")->fetch_row()[0],
        'rejected_donations' => $conn->query("SELECT COUNT(*) FROM donations WHERE donor_id = $donor_id AND status = 'rejected'")->fetch_row()[0],
        'approved_posts' => $conn->query("SELECT COUNT(*) FROM posts WHERE user_id = $donor_id AND status = 'approved'")->fetch_row()[0],
        'pending_posts' => $conn->query("SELECT COUNT(*) FROM posts WHERE user_id = $donor_id AND status = 'pending'")->fetch_row()[0]
    ];
    $stats = $conn->query("SELECT DATE_FORMAT(created_at, '%Y') AS year, COUNT(CASE WHEN status='approved' THEN 1 END) AS posts_approved, (SELECT COUNT(*) FROM donations d2 WHERE d2.status='collected' AND d2.donor_id = $donor_id AND YEAR(d2.created_at)=YEAR(p.created_at)) AS donations_collected FROM posts p WHERE p.user_id = $donor_id AND p.status='approved' GROUP BY year ORDER BY year DESC LIMIT 6");
   
    $topOrgs = $conn->query("SELECT u.fullname, COUNT(d.id) AS total_donations FROM users u JOIN donations d ON d.organization_id = u.id WHERE d.donor_id = $donor_id AND d.status = 'collected' AND YEAR(d.created_at) = YEAR(CURDATE()) GROUP BY u.id ORDER BY total_donations DESC LIMIT 5");
    $topDonors = $conn->query("SELECT u.fullname, COUNT(d.id) AS total FROM users u JOIN donations d ON d.donor_id = u.id WHERE d.status = 'collected' AND YEAR(d.created_at) = YEAR(CURDATE()) GROUP BY u.id ORDER BY total DESC LIMIT 5");
}

// Fetch list of organizations for the messaging feature
$organizations = [];
if ($view === 'messages') {
    $orgs_query = $conn->prepare("SELECT id, fullname, profile_picture FROM users WHERE role = 'organization' ORDER BY fullname ASC");
    $orgs_query->execute();
    $orgs_result = $orgs_query->get_result();
    while ($row = $orgs_result->fetch_assoc()) {
        $organizations[] = $row;
    }
    $orgs_query->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Donor Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="donordashboard.css">
</head>
<body>
<nav class="navbar">
    <a class="navbar-brand navbar-logo" href="#">
        <img src="images/logo.png" alt="Logo">
    </a>
</nav>

<div class="d-flex">
    <div class="col-md-2 sidebar d-none d-md-block">
        <div class="d-flex align-items-center mb-4">
            <img src="<?= htmlspecialchars($donor_info['profile_picture'] ?? '../uploads/profile_pictures/default-avatar.png') ?>" alt="Profile Picture" class="profile-thumbnail rounded-circle me-3">
            <h5 class="text-white mb-0"><?= htmlspecialchars($donor_info['fullname'] ?? 'Donor') ?></h5>
        </div>
        <hr class="text-white-50">
        <a href="dashboard.php" class="<?= $view === '' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
        <a href="dashboard.php?view=history" class="<?= $view === 'history' ? 'active' : '' ?>"><i class="fas fa-history me-2"></i> Donation History</a>
        <a href="dashboard.php?view=messages" class="<?= $view === 'messages' ? 'active' : '' ?>"><i class="fas fa-comments me-2"></i> Messages</a>
        <a href="create_donation.php"><i class="fas fa-hand-holding-heart me-2"></i> Post a Donation</a>
        <a href="../shared/community_posts.php"><i class="fas fa-users me-2"></i> Community Posts</a>
        <a href="create_post.php"><i class="fas fa-edit me-2"></i> Create Post</a>
        <a href="profile.php"><i class="fas fa-user-circle me-2"></i> Profile</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <div class="col-md-10 content">
        
        <hr>

        <?php if ($view === ''): ?>
            <h3 class="mb-4">Your Donation and Post Overview</h3>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card dashboard-approved text-center shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-white">Approved Donations</h5>
                <p class="display-5 text-white"><?= $counts['approved_donations'] ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-pending-donations text-center shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-white">Pending Donations</h5>
                <p class="display-5 text-white"><?= $counts['pending_donations'] ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-rejected text-center shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-white">Rejected Donations</h5>
                <p class="display-5 text-white"><?= $counts['rejected_donations'] ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card dashboard-approved-posts text-center shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-white">Approved Posts</h5>
                <p class="display-5 text-white"><?= $counts['approved_posts'] ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-pending-posts text-center shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-white">Pending Posts</h5>
                <p class="display-5 text-white"><?= $counts['pending_posts'] ?></p>
            </div>
        </div>
    </div>
            <div class="row mb-4">
                <div class="col-md-6"><div class="card shadow-sm p-4 h-100"><h4 class="card-title mb-3">Yearly Donation & Post Stats</h4><canvas id="yearlyChart"></canvas></div></div>
                <div class="col-md-6">
                    <div class="row h-100">
                       
                        <div class="col-12 mb-4"><div class="card shadow-sm p-4 h-100"><h4 class="card-title mb-3">Top 5 Organizations Donated To (This Year)</h4><ul class="list-group"><?php while($org = $topOrgs->fetch_assoc()): ?><li class="list-group-item d-flex justify-content-between"><span><?= htmlspecialchars($org['fullname']) ?></span><span class="badge bg-primary rounded-pill"><?= $org['total_donations'] ?></span></li><?php endwhile; ?><?php if(!$topOrgs->num_rows): ?><li class="list-group-item text-muted">No organizations found this year.</li><?php endif; ?></ul></div></div>
                        <div class="col-12"><div class="card shadow-sm p-4 h-100"><h4 class="card-title mb-3">Top 5 Donors (This Year)</h4><ul class="list-group"><?php while($d = $topDonors->fetch_assoc()): ?><li class="list-group-item d-flex justify-content-between"><span><?= htmlspecialchars($d['fullname']) ?></span><span class="badge bg-warning rounded-pill"><?= $d['total'] ?></span></li><?php endwhile; ?><?php if(!$topDonors->num_rows): ?><li class="list-group-item text-muted">No donors found this year.</li><?php endif; ?></ul></div></div>
                    </div>
                </div>
            </div>

        <?php elseif ($view === 'history'): ?>
            <h3 class="mb-4">Donation History</h3>
            <div class="card shadow-sm p-4 mb-4">
                <form method="get" action="" class="row g-3">
                    <input type="hidden" name="view" value="history">
                    <div class="col-md-4"><input type="text" name="search" placeholder="Search by Organization Name or Status" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control"></div>
                    <div class="col-md-3"><select name="sort" class="form-select"><option value="created_at" <?= ($_GET['sort'] ?? '') == 'created_at' ? 'selected' : '' ?>>Date</option><option value="status" <?= ($_GET['sort'] ?? '') == 'status' ? 'selected' : '' ?>>Status</option></select></div>
                    <div class="col-md-3"><select name="order" class="form-select"><option value="DESC" <?= ($_GET['order'] ?? '') == 'DESC' ? 'selected' : '' ?>>Descending</option><option value="ASC" <?= ($_GET['order'] ?? '') == 'ASC' ? 'selected' : '' ?>>Ascending</option></select></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Apply</button></div>
                </form>
            </div>
            <?php
                $search = $_GET['search'] ?? ''; $sort = $_GET['sort'] ?? 'created_at'; $order = $_GET['order'] ?? 'DESC'; $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; $limit = 10; $offset = ($page - 1) * $limit;
                $query = "SELECT SQL_CALC_FOUND_ROWS d.id AS donation_id, d.created_at, d.delivery_mode, d.caption, d.status, u.fullname AS organization_name, dc.within_expiration_date, dc.properly_stored, dc.no_damaged_packaging, dc.fresh_not_rotten, dc.no_contamination, dc.packaging_intact, dc.food_safely_prepared, GROUP_CONCAT(DISTINCT CONCAT(i.item_type, ':', i.quantity, ':', i.expiration_date) SEPARATOR '|') AS items_data, GROUP_CONCAT(DISTINCT img.image_path) AS images FROM donations d LEFT JOIN donation_conditions dc ON d.id = dc.donation_id LEFT JOIN donation_items i ON d.id = i.donation_id LEFT JOIN donation_images img ON d.id = img.donation_id LEFT JOIN users u ON d.organization_id = u.id WHERE d.donor_id = ?";
                if ($search) { $query .= " AND (u.fullname LIKE ? OR d.status LIKE ?)"; }
                $query .= " GROUP BY d.id ORDER BY $sort $order LIMIT ? OFFSET ?";
                $stmt = $conn->prepare($query);
                if ($search) { $like = "%$search%"; $stmt->bind_param("issii", $donor_id, $like, $like, $limit, $offset); } else { $stmt->bind_param("iii", $donor_id, $limit, $offset); }
                $stmt->execute(); $result = $stmt->get_result(); $total_result = $conn->query("SELECT FOUND_ROWS() AS total")->fetch_assoc()['total']; $total_pages = ceil($total_result / $limit);
            ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead><tr><th>Donation ID</th><th>Donate To</th><th>Date</th><th>Images</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['donation_id'] ?></td>
                            <td><?= htmlspecialchars($row['organization_name'] ?? 'N/A') ?></td>
                            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                            <td><?php if ($row['images']) { $imgs = array_map('trim', explode(",", $row['images'])); foreach ($imgs as $img) { echo "<img src='../{$img}' alt='Donation Image' class='image-thumbnail me-1' data-bs-toggle='modal' data-bs-target='#imageModal' data-image-url='../{$img}'>"; } } else { echo "<span class='text-muted'>N/A</span>"; } ?></td>
                            <td><?php $status_classes = ['approved' => 'bg-success', 'collected' => 'bg-primary', 'rejected' => 'bg-danger', 'pending' => 'bg-warning']; $status_class = $status_classes[strtolower($row['status'])] ?? 'bg-secondary'; ?><span class="badge <?= $status_class ?> rounded-pill"><?= htmlspecialchars(ucfirst($row['status'])) ?></span></td>
                            <td><button class="btn btn-sm btn-info text-white details-btn" data-bs-toggle="modal" data-bs-target="#donationDetailsModal" data-delivery-mode="<?= htmlspecialchars($row['delivery_mode']) ?>" data-caption="<?= htmlspecialchars($row['caption']) ?>" data-donation-conditions='<?= json_encode(['within_expiration_date' => $row['within_expiration_date'], 'properly_stored' => $row['properly_stored'], 'no_damaged_packaging' => $row['no_damaged_packaging'], 'fresh_not_rotten' => $row['fresh_not_rotten'], 'no_contamination' => $row['no_contamination'], 'packaging_intact' => $row['packaging_intact'], 'food_safely_prepared' => $row['food_safely_prepared']]) ?>' data-items='<?= json_encode(array_map(function($item) { $parts = explode(":", $item); return ['type' => $parts[0] ?? 'N/A', 'quantity' => $parts[1] ?? 'N/A', 'expiry' => $parts[2] ?? 'N/A']; }, array_filter(explode("|", $row['items_data'])))); ?>'>Details</button></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="6" class="text-center text-muted">No donation history found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <nav><ul class="pagination justify-content-center"><?php for ($i = 1; $i <= $total_pages; $i++): ?><li class="page-item <?= $page == $i ? 'active' : '' ?>"><a class="page-link" href="?view=history&page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>"><?= $i ?></a></li><?php endfor; ?></ul></nav>

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
        <?php endif; ?>

        <div class="modal fade" id="donationDetailsModal" tabindex="-1" aria-labelledby="donationDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="donationDetailsModalLabel">Donation Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p><strong>Caption:</strong> <span id="modal-caption"></span></p><hr><h6>Donation Items:</h6><ul id="modal-items-list" class="list-group mb-3"></ul><h6>Donation Conditions:</h6><ul id="modal-conditions-list" class="list-group mb-3"></ul><p><strong>Delivery Mode:</strong> <span id="modal-delivery-mode"></span></p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div></div></div>
        </div>
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="imageModalLabel">Donation Image</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body modal-body-image"><img src="" id="modal-image" alt="Donation Image" class="img-fluid"></div></div></div>
        </div>

        <!-- [NEW] Start New Chat Modal -->
        <div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newChatModalLabel">Start a New Chat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="organization-search" placeholder="Search for an organization...">
                        </div>
                        <ul id="organizations-list" class="list-group list-group-flush">
                            <?php foreach ($organizations as $org): ?>
                                <li class="list-group-item list-group-item-action d-flex align-items-center" data-org-id="<?= $org['id'] ?>" data-org-name="<?= htmlspecialchars($org['fullname']) ?>">
                                    <img src="<?= htmlspecialchars($org['profile_picture'] ?? '../uploads/profile_pictures/default-avatar.png') ?>" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    <strong><?= htmlspecialchars($org['fullname']) ?></strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($view === ''): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
// Details modal fill
document.addEventListener('DOMContentLoaded', function () {
    const view = '<?= $view ?>';
    const currentUserId = <?= $donor_id ?>;

    // --- Main Dashboard Logic ---
    if (view === '') {
        const years = [], postsData = [], donationsData = [];
        <?php if ($stats) { $stats->data_seek(0); } // Reset pointer for JS ?>
        <?php while($row = $stats?->fetch_assoc()): ?>
            years.unshift("<?= $row['year'] ?>");
            postsData.unshift(<?= $row['posts_approved'] ?>);
            donationsData.unshift(<?= $row['donations_collected'] ?>);
        <?php endwhile; ?>

        const chartCanvas = document.getElementById('yearlyChart');
        if (chartCanvas) {
            new Chart(chartCanvas, {
                type: 'bar',
                data: {
                    labels: years,
                    datasets: [{
                        label: 'Approved Posts', data: postsData, backgroundColor: '#4e73df'
                    }, {
                        label: 'Donations Collected', data: donationsData, backgroundColor: '#1cc88a'
                    }]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });
        }
    }

    // --- History Page Logic ---
    const detailsModal = document.getElementById('donationDetailsModal');
    if (detailsModal) {
        detailsModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const deliveryMode = button.getAttribute('data-delivery-mode');
            const donationConditionsJson = button.getAttribute('data-donation-conditions');
            const caption = button.getAttribute('data-caption');
            const itemsJson = button.getAttribute('data-items');
            let items = [], conditions = {};
            try { items = JSON.parse(itemsJson); conditions = JSON.parse(donationConditionsJson); } catch (e) { console.error("Error parsing JSON data:", e); }
            document.getElementById('modal-delivery-mode').textContent = deliveryMode;
            document.getElementById('modal-caption').textContent = caption;
            const conditionsList = document.getElementById('modal-conditions-list');
            conditionsList.innerHTML = '';
            for (const [key, value] of Object.entries(conditions)) {
                const li = document.createElement('li');
                const cleanKey = key.replace(/_/g, ' ');
                const iconClass = value == 1 ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
                li.className = 'list-group-item';
                li.innerHTML = `<i class="fas ${iconClass} me-2"></i><strong>${cleanKey}:</strong> ${value == 1 ? 'Yes' : 'No'}`;
                conditionsList.appendChild(li);
            }
            const itemsList = document.getElementById('modal-items-list');
            itemsList.innerHTML = '';
            if (items.length > 0) {
                items.forEach(item => {
                    const li = document.createElement('li'); li.className = 'list-group-item';
                    li.innerHTML = `<strong>Item:</strong> ${item.type}<br><strong>Quantity:</strong> ${item.quantity}<br><strong>Expiry Date:</strong> ${item.expiry}`;
                    itemsList.appendChild(li);
                });
            } else {
                itemsList.innerHTML = '<li class="list-group-item text-muted">No items specified.</li>';
            }
        });
    }
    document.querySelectorAll('.image-thumbnail').forEach(image => {
        image.addEventListener('click', function(event) {
            const imageUrl = event.target.getAttribute('data-image-url');
            document.getElementById('modal-image').src = imageUrl;
        });
    });

    // --- [NEW] Messaging Logic ---
    if (view === 'messages') {
        const conversationList = document.getElementById('conversation-list');
        const chatWelcome = document.getElementById('chat-welcome');
        const chatArea = document.getElementById('chat-area');
        const chatWithName = document.getElementById('chat-with-name');
        const chatBody = document.getElementById('chat-body');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        const organizationsList = document.getElementById('organizations-list');
        const organizationSearch = document.getElementById('organization-search');
        const newChatModal = new bootstrap.Modal(document.getElementById('newChatModal'));

        let activeRecipientId = null;
        let messageInterval = null;

        async function loadConversations() {
            try {
                const response = await fetch('actions/get_conversations.php');
                const conversations = await response.json();
                conversationList.innerHTML = '';
                if (conversations.length === 0) {
                    conversationList.innerHTML = '<p class="text-center p-3 text-muted">No conversations yet.</p>';
                } else {
                    conversations.forEach(convo => {
                        const convoItem = document.createElement('a');
                        convoItem.className = 'list-group-item list-group-item-action d-flex align-items-center';
                        convoItem.href = '#';
                        convoItem.dataset.recipientId = convo.id;
                        convoItem.dataset.recipientName = convo.fullname;
                        const profilePic = convo.profile_picture ? `../uploads/${convo.profile_picture}` : '../uploads/profile_pictures/default-avatar.png';
                        convoItem.innerHTML = `<img src="${profilePic}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;"><strong>${convo.fullname}</strong>`;
                        convoItem.addEventListener('click', (e) => {
                            e.preventDefault();
                            document.querySelectorAll('#conversation-list a').forEach(el => el.classList.remove('active'));
                            convoItem.classList.add('active');
                            activeRecipientId = convo.id;
                            chatWithName.textContent = convo.fullname;
                            chatWelcome.classList.add('d-none');
                            chatArea.classList.remove('d-none');
                            loadMessages(activeRecipientId);
                        });
                        conversationList.appendChild(convoItem);
                    });
                }
            } catch (error) {
                console.error('Failed to load conversations:', error);
                conversationList.innerHTML = '<p class="text-center p-3 text-danger">Could not load conversations.</p>';
            }
        }

 async function loadMessages(recipientId, scroll = true) {
    if (!recipientId) return;
    
    // The auto-refresh interval has been removed from this function.
    // We no longer need to clear it.

    try {
        const response = await fetch(`actions/get_messages.php?recipient_id=${recipientId}`);
        const messages = await response.json();
        
        chatBody.innerHTML = ''; // Clear existing messages
        
        messages.forEach(msg => {
            const div = document.createElement('div');
            div.className = 'message ' + (msg.sender_id == currentUserId ? 'sent' : 'received');

            const time = formatDateTime(msg.created_at);

            div.innerHTML = `
                <div>${msg.message_content}</div>
                <div class="small text-muted mt-1" style="font-size: 0.75rem;">${time}</div>
            `;
            // This correctly adds each message to the bottom of the chat window
            chatBody.appendChild(div);
        });

        // If scrolling is enabled (like on the first load or after sending a message)
        if (scroll) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
        
    } catch (error) {
        console.error("Failed to load messages:", error);
    }
}

messageForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const messageText = messageInput.value.trim();
    if (!messageText || !activeRecipientId) return;

    const messageData = { recipient_id: activeRecipientId, message: messageText };
    messageInput.value = ''; // Clear the input field immediately

    try {
        const response = await fetch('actions/send_message.php', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/json' }, 
            body: JSON.stringify(messageData) 
        });

        const result = await response.json();
        
        // If the message was sent successfully, reload the chat to show it
        if (result.success) {
            await loadMessages(activeRecipientId, true); // Reload and scroll to bottom
        } else {
            console.error('Failed to send message:', result.message);
        }
    } catch (error) {
        console.error('Error sending message:', error);
    }
});

        // Handle organization selection from modal
        organizationsList.addEventListener('click', function(e) {
            const orgItem = e.target.closest('li.list-group-item');
            if (orgItem) {
                const recipientId = orgItem.dataset.orgId;
                const recipientName = orgItem.dataset.orgName;
                
                // Set active chat and load messages
                activeRecipientId = recipientId;
                chatWithName.textContent = recipientName;
                chatWelcome.classList.add('d-none');
                chatArea.classList.remove('d-none');
                loadMessages(activeRecipientId);

                // Close the modal
                newChatModal.hide();
                
                // Update conversation list visual state
                document.querySelectorAll('#conversation-list a').forEach(el => el.classList.remove('active'));
            }
        });

        // Filter organizations based on search input
        organizationSearch.addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            document.querySelectorAll('#organizations-list li').forEach(li => {
                const orgName = li.dataset.orgName.toLowerCase();
                if (orgName.includes(searchText)) {
                    li.style.display = 'flex';
                } else {
                    li.style.display = 'none';
                }
            });
        });

        loadConversations();
    }
});
</script>
</body>
</html>
