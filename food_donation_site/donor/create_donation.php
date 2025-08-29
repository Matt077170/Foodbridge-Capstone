<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'donor') {
    header("Location: ../login.php");
    exit();
}

// Fetch organizations to populate dropdown
$org_result = $conn->query("SELECT id, fullname FROM users WHERE role = 'organization' AND status = 'approved'");
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/style.css">
    <title>Create Donation</title>
    <script>
        function toggleOtherField() {
            const otherCheckbox = document.getElementById('other-checkbox');
            document.getElementById('otherField').style.display = otherCheckbox.checked ? 'inline-block' : 'none';
        }
    </script>
</head>
<body>
<h2>Create a Donation</h2>
<form action="process_donation.php" method="post" enctype="multipart/form-data">
    <label>Caption:</label><br>
    <textarea name="caption" required></textarea><br><br>

    <label>Select Organization to Donate To:</label><br>
    <select name="organization_id" required>
        <option value="">-- Choose Organization --</option>
        <?php while ($org = $org_result->fetch_assoc()): ?>
            <option value="<?= $org['id'] ?>"><?= htmlspecialchars($org['fullname']) ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Food Types and Quantities:</label><br>
    <?php
    $food_types = ["Fresh Produce", "Packaged Goods", "Cooked Meats", "Bottled Water", "Others"];
    foreach ($food_types as $type):
        $safe_name = htmlspecialchars($type);
    ?>
	<label>
    <input type="checkbox" name="food_types[<?= $safe_name ?>]" value="1"
        <?= $type == "Others" ? "id='other-checkbox' onchange='toggleOtherField()'" : "" ?>>
    <?= $safe_name ?>
</label>

         <?php if ($type == "Others"): ?>
            <input type="text" name="other_description" id="otherField" placeholder="Specify other" style="display:none;">
        <?php endif; ?>
        <input type="number" name="quantities[<?= $safe_name ?>]" placeholder="Quantity">
        <input type="date" name="expirations[<?= $safe_name ?>]"><br>
		
    <?php endforeach; ?>
    <br>

    <label>Conditions:</label><br>
    <?php
    $conditions = [
        "within_expiration_date" => "Within expiration date",
        "properly_stored" => "Properly stored",
        "no_damaged_packaging" => "No damaged or opened packaging",
        "fresh_not_rotten" => "Fresh produce not rotten",
        "no_contamination" => "No signs of contamination",
        "packaging_intact" => "Packaging is intact and hygienic",
        "food_safely_prepared" => "Food prepared with safety standards"
    ];
    foreach ($conditions as $key => $label):
    ?>
        <label><input type="checkbox" name="conditions[]" value="<?= $key ?>"> <?= $label ?></label><br>
    <?php endforeach; ?>
    <br>

<label>Upload Donation Photos:</label><br>
<input type="file" name="photos[]" id="photoInput" accept="image/*" multiple><br><br>

<div id="previewContainer" style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:10px;"></div>
<ul id="skippedFiles" style="color: red;"></ul>

    <label>Delivery Mode:</label><br>
    <select name="delivery_mode" required>
        <option value="Drop-off">Drop-off</option>
        <option value="Pickup">Pickup</option>
        <option value="Courier">Courier</option>
    </select><br><br>

    <label><input type="checkbox" name="same_day_delivery"> Deliver within the day</label><br><br>

    <input type="submit" value="Submit Donation">
</form>
<br>
<a href="dashboard.php">Back to Dashboard</a>
<script>
document.getElementById('photoInput').addEventListener('change', function (event) {
    const files = event.target.files;
    const previewContainer = document.getElementById('previewContainer');
    const skippedList = document.getElementById('skippedFiles');
    const maxFileSize = 2 * 1024 * 1024; // 2MB per file
    const maxTotalSize = 10 * 1024 * 1024; // 10MB total
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    previewContainer.innerHTML = '';
    skippedList.innerHTML = '';

    let totalSize = 0;

    for (let i = 0; i < files.length; i++) {
        const file = files[i];

        // Check file type
        if (!allowedTypes.includes(file.type)) {
            const li = document.createElement('li');
            li.textContent = `${file.name} skipped (invalid type)`;
            skippedList.appendChild(li);
            continue;
        }

        // Check file size
        if (file.size > maxFileSize) {
            const li = document.createElement('li');
            li.textContent = `${file.name} skipped (over 2MB)`;
            skippedList.appendChild(li);
            continue;
        }

        // Check total size
        if ((totalSize + file.size) > maxTotalSize) {
            const li = document.createElement('li');
            li.textContent = `${file.name} skipped (total size exceeds 10MB)`;
            skippedList.appendChild(li);
            continue;
        }

        totalSize += file.size;

        // Create image preview
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100px';
            img.style.height = '100px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '6px';
            img.style.boxShadow = '0 0 5px rgba(0,0,0,0.2)';
            previewContainer.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>
