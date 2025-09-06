<?php
session_start();
include '../includes/db.php';

// Check for valid session and role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'donor') {
    header("Location: ../login.php");
    exit();
}

$donor_id = $_SESSION['user']['id'];

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data
    $caption = $_POST['caption'];
    $organization_id = $_POST['organization_id'];
    $delivery_mode = $_POST['delivery_mode'];
    $food_types = $_POST['food_types'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $expirations = $_POST['expirations'] ?? [];
    $conditions = $_POST['conditions'] ?? [];

    // Combine conditions into a JSON string for database storage
    $condition_details = json_encode($conditions);

    try {
        // Begin a transaction to ensure atomicity
        $conn->begin_transaction();

        // 1. Insert the main donation record
        $stmt_donation = $conn->prepare("INSERT INTO donations (donor_id, org_id, caption, status, delivery_mode, created_at) VALUES (?, ?, ?, 'pending', ?, NOW())");
        $stmt_donation->bind_param("iisss", $donor_id, $organization_id, $caption, $delivery_mode);
        $stmt_donation->execute();
        $donation_id = $conn->insert_id;

        // 2. Process each donation item and insert into a separate table (e.g., `donation_items`)
        $stmt_item = $conn->prepare("INSERT INTO donation_items (donation_id, item_type, quantity, expiration_date) VALUES (?, ?, ?, ?)");
        foreach ($food_types as $type => $is_checked) {
            // Only process checked items with a quantity
            if ($is_checked && !empty($quantities[$type]) && $quantities[$type] > 0) {
                $quantity = $quantities[$type];
                $expiration = $expirations[$type];
                // For "Others", use the specified description
                $item_type = ($type === 'Others' && !empty($_POST['other_description'])) ? $_POST['other_description'] : $type;
                $stmt_item->bind_param("isss", $donation_id, $item_type, $quantity, $expiration);
                $stmt_item->execute();
            }
        }
        $stmt_item->close();

        // 3. Process image uploads
        $upload_dir = '../uploads/';
        $stmt_photo = $conn->prepare("INSERT INTO donation_images (donation_id, image_path) VALUES (?, ?)");

        // Iterate through each uploaded file in the `$_FILES` array
        if (!empty($_FILES['photos']['name'][0])) {
            foreach ($_FILES['photos']['name'] as $key => $name) {
                $file_name = $_FILES['photos']['name'][$key];
                $file_tmp = $_FILES['photos']['tmp_name'][$key];
                $file_error = $_FILES['photos']['error'][$key];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                // Validate file upload for security
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                if ($file_error === UPLOAD_ERR_OK && in_array($file_ext, $allowed_extensions)) {
                    // Generate a unique file name to prevent overwrites
                    $unique_name = uniqid('photo_', true) . '.' . $file_ext;
                    $file_path = $upload_dir . $unique_name;

                    // Move the uploaded file to the permanent directory
                    if (move_uploaded_file($file_tmp, $file_path)) {
                        // Insert the photo path linked to the main donation ID
                        $stmt_photo->bind_param("is", $donation_id, $file_path);
                        $stmt_photo->execute();
                    }
                }
            }
        }
        $stmt_photo->close();

        // 4. Update the main donation record with conditions
        $stmt_conditions = $conn->prepare("UPDATE donations SET conditions = ? WHERE id = ?");
        $stmt_conditions->bind_param("si", $condition_details, $donation_id);
        $stmt_conditions->execute();
        $stmt_conditions->close();

        // Commit the transaction
        $conn->commit();

        // Redirect to a success page
        header("Location: dashboard.php?success=donation_created");
        exit();

    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        // Log the error for debugging
        error_log("Failed to create donation: " . $e->getMessage());
        // Redirect with an error message
        header("Location: dashboard.php?error=donation_failed");
        exit();
    }
}

// Redirect if accessed directly without POST data
header("Location: create_donation.php");
exit();
?>
