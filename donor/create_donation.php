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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a Donation</title>
	<link rel="stylesheet" href="donation.css">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        function toggleOtherField() {
            const otherCheckbox = document.getElementById('other-checkbox');
            document.getElementById('otherField').style.display = otherCheckbox.checked ? 'inline-block' : 'none';
        }
    </script>
</head>

<body class="relative flex items-center justify-center min-h-screen p-4">

    <!-- Form Container Card -->
    <div class="form-container w-full max-w-2xl bg-white shadow-xl rounded-2xl p-8 md:p-10 transition-all duration-300">

        <!-- Header -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 border-b pb-4 border-gray-200">
            <div class="flex items-center">
                <h1 class="text-3xl font-bold text-gray-800">Create a Donation</h1>
            </div>
            <a href="dashboard.php" class="mt-2 md:mt-0 text-sm font-semibold text-gray-500 hover:text-emerald-600 transition duration-200">
                Back to Dashboard
            </a>
        </div>

        <form action="process_donation.php" method="post" enctype="multipart/form-data">

            <!-- Caption Section -->
            <div class="mb-6">
                <label for="caption" class="block text-sm font-medium text-gray-700 mb-2">Caption:</label>
                <textarea id="caption" name="caption" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300 resize-none placeholder-gray-400" placeholder="Describe the donation in a few sentences..."></textarea>
            </div>

            <!-- Organization Selection Section -->
            <div class="mb-6">
                <label for="organization_id" class="block text-sm font-medium text-gray-700 mb-2">Select Organization to Donate To:</label>
                <select id="organization_id" name="organization_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                    <option value="">-- Choose Organization --</option>
                    <?php while ($org = $org_result->fetch_assoc()): ?>
                    <option value="<?= $org['id'] ?>"><?= htmlspecialchars($org['fullname']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Food Types and Quantities Section -->
            <div class="bg-gray-50 rounded-2xl p-6 shadow-sm mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Food Types and Quantities:</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    $food_types = ["Fresh Produce", "Packaged Goods", "Cooked Meats", "Bottled Water", "Others"];
                    foreach ($food_types as $type):
                        $safe_name = htmlspecialchars($type);
                    ?>
                    <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center mb-2">
                            <input type="checkbox" name="food_types[<?= $safe_name ?>]" value="1" <?= $type == "Others" ? "id='other-checkbox' onchange='toggleOtherField()'" : "" ?> class="h-4 w-4 text-emerald-600 border-gray-300 rounded-sm focus:ring-emerald-500">
                            <label for="<?= str_replace(' ', '-', strtolower($safe_name)) ?>" class="ml-2 text-sm font-medium text-gray-700"><?= $safe_name ?></label>
                        </div>
                        <div class="space-y-2 mt-2">
                            <input type="number" name="quantities[<?= $safe_name ?>]" placeholder="Quantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <input type="date" name="expirations[<?= $safe_name ?>]" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </div>
                        <?php if ($type == "Others"): ?>
                        <input type="text" name="other_description" id="otherField" placeholder="Specify other" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm mt-2 focus:outline-none focus:ring-1 focus:ring-emerald-500" style="display:none;">
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Conditions Section -->
            <div class="bg-gray-50 rounded-2xl p-6 shadow-sm mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Conditions:</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    <label class="flex items-center space-x-2 text-gray-700">
                        <input type="checkbox" name="conditions[<?= $key ?>]" value="1" class="h-4 w-4 text-emerald-600 border-gray-300 rounded-sm focus:ring-emerald-500">
                        <span><?= $label ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Upload Photos Section -->
            <div class="mb-6">
                <label for="photoInput" class="block text-sm font-medium text-gray-700 mb-2">Upload Donation Photos:</label>
                <div class="relative flex items-center justify-center border-2 border-dashed border-gray-300 rounded-xl p-8 cursor-pointer transition-colors duration-300 hover:border-emerald-500 hover:bg-gray-50">
                    <div class="text-center">
                        <p class="mt-2 text-sm text-gray-600 font-semibold">
                            Drag and drop files here or
                            <span class="text-emerald-600 font-bold">browse</span>
                        </p>
                        <p class="text-xs text-gray-400">PNG, JPG, GIF (max 2MB per file, 10MB total)</p>
                    </div>
                    <input type="file" name="photos[]" id="photoInput" accept="image/*" multiple class="absolute inset-0 opacity-0 cursor-pointer">
                </div>
                <div id="previewContainer" class="flex flex-wrap gap-4 mt-4"></div>
                <ul id="skippedFiles" class="list-none text-red-500 text-sm mt-2"></ul>
            </div>

            <!-- Delivery Mode Section -->
            <div class="mb-8">
                <label for="delivery_mode" class="block text-sm font-medium text-gray-700 mb-2">Delivery Mode:</label>
                <select id="delivery_mode" name="delivery_mode" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-300">
                    <option value="Drop-off">Drop-off</option>
                    <option value="Pickup">Pickup</option>
                    <option value="Courier">Courier</option>
                </select>
            </div>

            <!-- Submit Button -->
            <input type="submit" value="Submit Donation" class="w-full bg-emerald-600 text-white font-semibold py-4 rounded-xl hover:bg-emerald-700 transition duration-300 ease-in-out shadow-lg cursor-pointer">
        </form>
    </div>

    <script>
        document.getElementById('photoInput').addEventListener('change', function(event) {
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
                reader.onload = function(e) {
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
