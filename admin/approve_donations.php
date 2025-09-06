
<?php
// FILE: approve_donations.php
// This is now a self-contained file handling both data fetching and presentation.

session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Check if an AJAX request is made to fetch data
if (isset($_GET['type'])) {
    header('Content-Type: application/json');
    $type = $_GET['type'];

    try {
        // Base SQL query
        $base_sql = "SELECT
                        d.id,
                        u.fullname AS donor_name,
                        u.address AS location,
                        d.caption,
                        d.status,
                        d.created_at AS donation_date,
                        GROUP_CONCAT(di.image_path) AS images
                    FROM donations d
                    JOIN users u ON d.donor_id = u.id
                    LEFT JOIN donation_images di ON d.id = di.donation_id";

        $where_clauses = [];
        $params = [];
        $types = '';

        if ($type === 'pending') {
            $where_clauses[] = "d.status = ?";
            $params[] = 'pending';
            $types .= 's';
        } else { // This handles the 'history' tab
            // Check if a specific status filter is provided and is not empty
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $where_clauses[] = "d.status = ?";
                $params[] = $_GET['status'];
                $types .= 's';
            } else {
                // For 'All' history, get everything that is NOT pending
                $where_clauses[] = "d.status != ?";
                $params[] = 'pending';
                $types .= 's';
            }
        }
        
        // Append WHERE clauses if they exist
        if (!empty($where_clauses)) {
            $base_sql .= " WHERE " . implode(' AND ', $where_clauses);
        }
        
        $sql = $base_sql . " GROUP BY d.id ORDER BY d.created_at DESC";

        $stmt = $conn->prepare($sql);
        
        // Bind parameters if any
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $donations = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();

        echo json_encode(['success' => true, 'donations' => $donations]);
        exit();

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching donations: ' . $e->getMessage()]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Approve Donations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="approvedonations.css"/>
</head>

<header class="navbar-header">
    <div class="navbar-content">
        <div class="logo-section">
            <img src="images/logo.png" alt="Food Bridge Logo" class="navbar-logo">
        </div>
    </div>
</header>


<div class="container py-4">

    <div id="statusAlert" class="custom-alert alert alert-success d-flex align-items-center" role="alert" style="display: none;">
        <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-check-circle-fill me-2" viewBox="0 0 16 16" width="24" height="24">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.497 5.253 7.273a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
        <div id="alertMessage"></div>
    </div>
    
    <div class="d-flex justify-content-between align-items-center">
        <h2>Approve Donations</h2>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
    <hr>

    <ul class="nav nav-tabs" id="donationTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">Pending</button></li>
        <li class="nav-item"><button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">History</button></li>
    </ul>

    <div class="tab-content mt-3">
        <div class="tab-pane fade show active" id="pending">
            <p class="text-muted text-center mt-3" id="pending-loading" style="display: none;">Loading pending donations...</p>
            <div id="pending-table-container" class="table-responsive"></div>
        </div>

        <div class="tab-pane fade" id="history">
            <div class="d-flex align-items-center mb-3">
                <label for="statusFilter" class="form-label me-2 mb-0">Filter by Status:</label>
                <select id="statusFilter" class="form-select w-auto me-2">
                    <option value="">All</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="collected">Collected</option>
                </select>
                <button id="applyFilterBtn" class="btn btn-primary">Apply Filter</button>
            </div>
            <p class="text-muted text-center mt-3" id="history-loading" style="display: none;">Loading history...</p>
            <div id="history-table-container" class="table-responsive"></div>
        </div>
    </div>

    <div class="modal fade" id="donationDetailsModal" tabindex="-1" aria-labelledby="donationDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="donationDetailsModalLabel">Donation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="donationDetailsContent">
                    </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imageViewerModal" tabindex="-1" aria-labelledby="imageViewerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageViewerModalLabel">Donation Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Full-size Donation Image">
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            loadDonations('pending');

            document.getElementById('donationTabs').addEventListener('shown.bs.tab', function (event) {
                const activeTab = event.target.getAttribute('data-bs-target');
                if (activeTab === '#pending') {
                    loadDonations('pending');
                } else if (activeTab === '#history') {
                    const status = document.getElementById('statusFilter').value;
                    loadDonations('history', 1, status);
                }
            });

            document.getElementById('applyFilterBtn').addEventListener('click', function() {
                const status = document.getElementById('statusFilter').value;
                loadDonations('history', 1, status);
            });

            document.body.addEventListener('click', handleDonationAction);

            // [NEW] Event listener for viewing images
            document.body.addEventListener('click', function(event) {
                const viewBtn = event.target.closest('.view-image-btn');
                if (viewBtn) {
                    event.preventDefault(); 
                    const imageSrc = viewBtn.dataset.imgSrc;
                    const modalImage = document.getElementById('modalImage');
                    if (modalImage) {
                        modalImage.src = imageSrc;
                    }
                }
            });
        });

        async function handleDonationAction(event) {
            const target = event.target;
            // Prevent this from firing when clicking an image inside the table
            if (target.closest('.view-image-btn')) {
                return;
            }
            if (target.classList.contains('approve-btn') || target.classList.contains('reject-btn')) {
                event.preventDefault();
                const row = target.closest('tr');
                const donationId = row.dataset.donationId;
                const status = target.dataset.status;

                if (!donationId || !status) {
                    showAlert('Error: Missing donation ID or status.', 'danger');
                    return;
                }
                const originalText = target.textContent;
                target.textContent = 'Processing...';
                target.disabled = true;

                const data = { id: donationId, action: status };
                try {
                    const response = await fetch('handle_donation.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });
                    const result = await response.json();
                    if (result.success) {
                        showAlert(result.message, 'success');
                        const activeTabButton = document.querySelector('.nav-link.active');
                        if (activeTabButton) {
                            const activeTabId = activeTabButton.getAttribute('data-bs-target').substring(1);
                            if (activeTabId === 'pending') {
                                loadDonations('pending');
                            } else if (activeTabId === 'history') {
                                const currentStatus = document.getElementById('statusFilter').value;
                                loadDonations('history', 1, currentStatus);
                            }
                        }
                    } else {
                        showAlert(result.message || 'An unexpected error occurred.', 'danger');
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    showAlert('An unexpected error occurred. Please check the console.', 'danger');
                } finally {
                    target.textContent = originalText;
                    target.disabled = false;
                }
            } else if (target.classList.contains('undo-btn')) {
                event.preventDefault();
                const row = target.closest('tr');
                const donationId = row.dataset.donationId;
                
                if (!donationId) {
                    showAlert('Error: Missing donation ID.', 'danger');
                    return;
                }
                
                const originalText = target.textContent;
                target.textContent = 'Undoing...';
                target.disabled = true;
                
                const data = { id: donationId };
                
                try {
                    const response = await fetch('undo_donation.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showAlert(result.message, 'success');
                        const currentStatus = document.getElementById('statusFilter').value;
                        loadDonations('history', 1, currentStatus);
                    } else {
                        showAlert(result.message || 'An unexpected error occurred.', 'danger');
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    showAlert('An unexpected error occurred. Please check the console.', 'danger');
                } finally {
                    target.textContent = originalText;
                    target.disabled = false;
                }
            } else if (target.classList.contains('details-btn')) {
                event.preventDefault();
                const row = target.closest('tr');
                if (row) {
                    const donationId = row.dataset.donationId;
                    if (donationId) {
                        viewDetails(donationId);
                    } else {
                        console.error('Error: Could not find donation ID.');
                    }
                } else {
                    console.error('Error: Could not find parent row for details button.');
                }
            }
        }

        function showAlert(message, type) {
            const alertBox = document.getElementById('statusAlert');
            const alertMessage = document.getElementById('alertMessage');
            
            alertBox.className = `custom-alert alert alert-${type} d-flex align-items-center`;
            alertMessage.textContent = message;
            alertBox.style.display = 'block';
            
            setTimeout(() => {
                alertBox.classList.remove('show');
                setTimeout(() => {
                    alertBox.style.display = 'none';
                }, 500);
            }, 3000);
            
            alertBox.classList.add('show');
        }

     function loadDonations(type, page = 1, status = '') {
            const container = document.getElementById(type + '-table-container');
            const loading = document.getElementById(type + '-loading');
            loading.style.display = 'block';
            container.innerHTML = '';

            let url = `approve_donations.php?type=${type}`;
            if (type === 'history' && status) {
                url += `&status=${status}`;
            }

            // [FIX] Added cache option to prevent browser from showing stale data
            fetch(url, { cache: 'no-store' })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP Error: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    loading.style.display = 'none';
                    if (!data.success) {
                        container.innerHTML = `<p class="text-danger text-center">${data.message}</p>`;
                        return;
                    }
                    renderDonationsTable(data.donations, type);
                })
                .catch(error => {
                    loading.style.display = 'none';
                    container.innerHTML = `<p class="text-danger text-center">Failed to load data. Error: ${error.message}</p>`;
                    console.error('Error fetching donations:', error);
                });
        }
        
        function renderDonationsTable(donations, type) {
            const container = document.getElementById(type + '-table-container');
            if (donations.length === 0) {
                container.innerHTML = `<p class="text-muted text-center mt-3">No ${type} donations found.</p>`;
                return;
            }

            let html = `
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Donor</th>
                            <th>Caption</th>
                            <th>Image(s)</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            donations.forEach(donation => {
                const statusColor = {
                    'pending': 'bg-warning',
                    'approved': 'bg-success',
                    'rejected': 'bg-danger',
                    'collected': 'bg-info'
                };
                
                const buttons = (donation.status === 'pending')
                    ? `<button class="btn btn-sm btn-success approve-btn" data-status="approved">Approve</button>
                       <button class="btn btn-sm btn-danger reject-btn" data-status="rejected">Reject</button>`
                    : `<button class="btn btn-sm btn-warning undo-btn">Undo</button>`;
                
                // [MODIFIED] This section now creates clickable image links
                let imageHtml = '';
                if (donation.images) {
                    const images = donation.images.split(',');
                    images.forEach(path => {
                        const fullPath = `../${path}`;
                        imageHtml += `
                            <a href="#" class="view-image-btn" data-bs-toggle="modal" data-bs-target="#imageViewerModal" data-img-src="${fullPath}">
                                <img src="${fullPath}" alt="Donation Image" class="img-thumbnail me-1">
                            </a>
                        `;
                    });
                } else {
                    imageHtml = 'No image';
                }

                html += `
                    <tr data-donation-id="${donation.id}">
                        <td>${donation.id}</td>
                        <td>${donation.donor_name}</td>
                        <td>${donation.caption}</td>
                        <td>${imageHtml}</td>
                        <td>${new Date(donation.donation_date).toLocaleDateString()}</td>
                        <td><span class="badge ${statusColor[donation.status]}">${donation.status.charAt(0).toUpperCase() + donation.status.slice(1)}</span></td>
                        <td>
                            <button class="btn btn-sm btn-info details-btn">Details</button>
                            ${buttons}
                        </td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
            `;
            
            container.innerHTML = html;
        }

 // Replace the old viewDetails function with this one

function viewDetails(id) {
    fetch(`fetch_donation_details.php?id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const modalBody = document.getElementById('donationDetailsContent');
            if (data.success) {
                let htmlContent = '';

                // --- [NEW] Delivery Mode Section ---
                if (data.delivery_mode) {
                    const deliveryMode = data.delivery_mode.charAt(0).toUpperCase() + data.delivery_mode.slice(1);
                    htmlContent += `<h5>Delivery Mode:</h5><p><strong>${deliveryMode}</strong></p><hr>`;
                }
                // --- End of New Section ---
                
                if (data.items && data.items.length > 0) {
                    htmlContent += '<h5>Donated Items:</h5><ul>';
                    data.items.forEach(item => {
                        htmlContent += `<li><strong>${item.quantity} ${item.item_type}</strong> (Expires: ${item.expiration_date})</li>`;
                    });
                    htmlContent += '</ul>';
                } else {
                    htmlContent += `<p>No items provided.</p>`;
                }

                if (data.conditions && Object.keys(data.conditions).length > 0) {
                    htmlContent += '<hr><h5>Conditions:</h5><ul>';
                    for (const [key, value] of Object.entries(data.conditions)) {
                        const formattedKey = key.replace(/_/g, ' ');
                        const statusText = value ? 'Yes' : 'No';
                        htmlContent += `<li><strong>${formattedKey.charAt(0).toUpperCase() + formattedKey.slice(1)}:</strong> ${statusText}</li>`;
                    }
                    htmlContent += '</ul>';
                } else {
                    htmlContent += `<hr><p>No condition details provided.</p>`;
                }

                modalBody.innerHTML = htmlContent;
                new bootstrap.Modal(document.getElementById('donationDetailsModal')).show();
            } else {
                modalBody.innerHTML = `<p class="text-danger">${data.message}</p>`;
                new bootstrap.Modal(document.getElementById('donationDetailsModal')).show();
            }
        })
        .catch(error => {
            console.error('Error fetching donation details:', error);
            const modalBody = document.getElementById('donationDetailsContent');
            modalBody.innerHTML = `<p class="text-danger">Failed to fetch details. Please check the console.</p>`;
        });
}
    </script>
</body>
</html>