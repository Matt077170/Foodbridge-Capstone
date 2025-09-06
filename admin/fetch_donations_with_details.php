<?php
// FILE: fetch_donations_with_details.php

session_start();
// Include the database connection file.
include '../includes/db.php';

header('Content-Type: text/html');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo '<p class="text-danger text-center">Access denied. You must be an admin to view this page.</p>';
    exit();
}

if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo '<p class="text-danger text-center">Failed to connect to the database. Please check your db.php file.</p>';
    exit();
}

$type = isset($_GET['type']) ? $_GET['type'] : 'pending';
$status = ($type === 'pending') ? 'pending' : 'approved';

// The correct SQL query using a JOIN on donor_id and a LEFT JOIN for images
$sql = "SELECT
            d.id,
            u.fullname AS donor_name,
            u.address AS location,
            d.caption,
            d.status,
            d.created_at AS donation_date,
            GROUP_CONCAT(di.image_path) AS images
        FROM donations d
        JOIN users u ON d.donor_id = u.id
        LEFT JOIN donation_images di ON d.id = di.donation_id
        WHERE d.status = ?
        GROUP BY d.id
        ORDER BY d.created_at DESC";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo '<p class="text-danger text-center">Failed to prepare SQL statement: ' . htmlspecialchars($conn->error) . '</p>';
    $conn->close();
    exit();
}

$stmt->bind_param("s", $status);

if (!$stmt->execute()) {
    http_response_code(500);
    echo '<p class="text-danger text-center">Failed to execute SQL query: ' . htmlspecialchars($stmt->error) . '</p>';
    $stmt->close();
    $conn->close();
    exit();
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '
    <table class="table table-striped table-hover mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Donor Name</th>
                <th>Location</th>
                <th>Caption</th>
                <th>Image(s)</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>';

    while ($row = $result->fetch_assoc()) {
        echo '
            <tr data-donation-id="' . htmlspecialchars($row['id']) . '">
                <td>' . htmlspecialchars($row['id']) . '</td>
                <td>' . htmlspecialchars($row['donor_name']) . '</td>
                <td>' . htmlspecialchars($row['location']) . '</td>
                <td>' . htmlspecialchars($row['caption']) . '</td>
                <td>';
        
        // Display images if they exist
        if (!empty($row['images'])) {
            $imagePaths = explode(',', $row['images']);
            foreach ($imagePaths as $path) {
                // Adjust the image path if necessary
                echo '<img src="' . htmlspecialchars($path) . '" alt="Donation Image" class="img-thumbnail" style="max-width: 100px; height: auto; margin-right: 5px;">';
            }
        } else {
            echo 'No image';
        }

        echo '</td>
                <td>' . htmlspecialchars(date('M j, Y', strtotime($row['donation_date']))) . '</td>
                <td><span class="badge ' . ($row['status'] == 'pending' ? 'bg-warning text-dark' : 'bg-success') . '">' . htmlspecialchars(ucfirst($row['status'])) . '</span></td>
                <td>
                    <button class="btn btn-sm btn-info details-btn">Details</button>';
        
        if ($row['status'] == 'pending') {
            echo '
                    <button class="btn btn-sm btn-success approve-btn" data-status="approve">Approve</button>
                    <button class="btn btn-sm btn-danger reject-btn" data-status="reject">Reject</button>';
        }
        echo '
                </td>
            </tr>';
    }

    echo '
        </tbody>
    </table>';
} else {
    echo '<p class="text-muted text-center">No ' . htmlspecialchars($status) . ' donations found.</p>';
}

$stmt->close();
$conn->close();
?>
