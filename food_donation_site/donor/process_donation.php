<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'donor') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = $_POST['caption'];
    $delivery_mode = $_POST['delivery_mode'];
    $same_day = isset($_POST['same_day_delivery']) ? 1 : 0;
    $organization_id = intval($_POST['organization_id']);
    $donor_id = $_SESSION['user']['id'];

    // Insert into donations table
    $stmt = $conn->prepare("INSERT INTO donations (donor_id, organization_id, caption, delivery_mode, same_day_delivery) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissi", $donor_id, $organization_id, $caption, $delivery_mode, $same_day);
    $stmt->execute();
    $donation_id = $stmt->insert_id;

    // Insert food items with quantity and expiration
    if (!empty($_POST['food_types']) && !empty($_POST['quantities'])) {
        foreach ($_POST['food_types'] as $label => $checked) {
            $qty = intval($_POST['quantities'][$label] ?? 0);
            $exp = $_POST['expirations'][$label] ?? null;
            if ($qty > 0) {
                $type_label = $label === "Others" ? ($_POST['other_description'] ?? "Others") : $label;
                $stmtFood = $conn->prepare("INSERT INTO donation_items (donation_id, item_type, quantity, expiration_date) VALUES (?, ?, ?, ?)");
                $stmtFood->bind_param("isis", $donation_id, $type_label, $qty, $exp);
                $stmtFood->execute();
            }
        }
    }

    // Handle condition checklist
    $all_conditions = [
        "within_expiration_date",
        "properly_stored",
        "no_damaged_packaging",
        "fresh_not_rotten",
        "no_contamination",
        "packaging_intact",
        "food_safely_prepared"
    ];
    $condition_flags = [];
    foreach ($all_conditions as $cond) {
        $condition_flags[$cond] = in_array($cond, $_POST['conditions'] ?? []) ? 1 : 0;
    }

    $stmtCond = $conn->prepare("INSERT INTO donation_conditions (
        donation_id,
        within_expiration_date,
        properly_stored,
        no_damaged_packaging,
        fresh_not_rotten,
        no_contamination,
        packaging_intact,
        food_safely_prepared
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtCond->bind_param(
        "iiiiiiii",
        $donation_id,
        $condition_flags["within_expiration_date"],
        $condition_flags["properly_stored"],
        $condition_flags["no_damaged_packaging"],
        $condition_flags["fresh_not_rotten"],
        $condition_flags["no_contamination"],
        $condition_flags["packaging_intact"],
        $condition_flags["food_safely_prepared"]
    );
    $stmtCond->execute();

    // Ensure uploads/ directory exists
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Handle image uploads
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
            $originalName = basename($_FILES['photos']['name'][$index]);
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $fileName = time() . "_" . uniqid() . "." . $extension;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $stmtImg = $conn->prepare("INSERT INTO donation_images (donation_id, image_path) VALUES (?, ?)");
                $stmtImg->bind_param("is", $donation_id, $fileName);
                $stmtImg->execute();
            }
        }
    }

    header("Location: dashboard.php");
    exit();
}
?>
