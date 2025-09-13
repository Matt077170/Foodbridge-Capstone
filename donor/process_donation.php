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

    try {
        // Begin a transaction to ensure atomicity
        $conn->begin_transaction();

        // 1. Insert the main donation record (FIXED)
        $stmt_donation = $conn->prepare("INSERT INTO donations (donor_id, organization_id, caption, status, delivery_mode, created_at) VALUES (?, ?, ?, 'pending', ?, NOW())");
        $stmt_donation->bind_param("iiss", $donor_id, $organization_id, $caption, $delivery_mode);
        $stmt_donation->execute();
        $donation_id = $conn->insert_id;
        $stmt_donation->close();


        // 2. Process each donation item and insert into a separate table
        $stmt_item = $conn->prepare("INSERT INTO donation_items (donation_id, item_type, quantity, expiration_date) VALUES (?, ?, ?, ?)");
        foreach ($food_types as $type => $is_checked) {
            // Only process checked items with a quantity
            if ($is_checked && !empty($quantities[$type]) && $quantities[$type] > 0) {
                $quantity = $quantities[$type];
                $expiration = !empty($expirations[$type]) ? $expirations[$type] : NULL;
                // For "Others", use the specified description
                $item_type = ($type === 'Others' && !empty($_POST['other_description'])) ? $_POST['other_description'] : $type;
                $stmt_item->bind_param("isis", $donation_id, $item_type, $quantity, $expiration);
                $stmt_item->execute();
            }
        }
        $stmt_item->close();

        // 3. Process image uploads
        $upload_dir = '../uploads/donations/'; // It's good practice to have a subfolder
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $stmt_photo = $conn->prepare("INSERT INTO donation_images (donation_id, image_path) VALUES (?, ?)");

        if (!empty($_FILES['photos']['name'][0])) {
            foreach ($_FILES['photos']['name'] as $key => $name) {
                if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['photos']['tmp_name'][$key];
                    $file_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                    if (in_array($file_ext, $allowed_extensions)) {
                        $unique_name = 'img_' . uniqid('', true) . '.' . $file_ext;
                        $file_path = $upload_dir . $unique_name;
                        
                        if (move_uploaded_file($file_tmp, $file_path)) {
                            // Store a relative path for portability
                            $relative_path = 'uploads/donations/' . $unique_name;
                            $stmt_photo->bind_param("is", $donation_id, $relative_path);
                            $stmt_photo->execute();
                        }
                    }
                }
            }
        }
        $stmt_photo->close();

        // 4. Insert the donation conditions into the `donation_conditions` table (FIXED)
        $stmt_conditions = $conn->prepare("INSERT INTO donation_conditions (donation_id, within_expiration_date, properly_stored, no_damaged_packaging, fresh_not_rotten, no_contamination, packaging_intact, food_safely_prepared) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $within_exp = isset($conditions['within_expiration_date']) ? '1' : '0';
        $properly_stored = isset($conditions['properly_stored']) ? '1' : '0';
        $no_damaged = isset($conditions['no_damaged_packaging']) ? '1' : '0';
        $fresh = isset($conditions['fresh_not_rotten']) ? '1' : '0';
        $no_contam = isset($conditions['no_contamination']) ? '1' : '0';
        $intact = isset($conditions['packaging_intact']) ? '1' : '0';
        $safely_prepared = isset($conditions['food_safely_prepared']) ? '1' : '0';

        $stmt_conditions->bind_param("isssssss", $donation_id, $within_exp, $properly_stored, $no_damaged, $fresh, $no_contam, $intact, $safely_prepared);
        $stmt_conditions->execute();
        $stmt_conditions->close();


        // Commit the transaction
        $conn->commit();

        // Redirect to a success page
        header("Location: create_donation.php?success=donation_created");
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