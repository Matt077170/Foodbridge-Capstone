<?php
namespace App;
class DonorProfileManager {
    private $conn;
    private $uploadDir;

    public function __construct($dbConnection, $uploadDir = '../uploads/profile_pictures/') {
        $this->conn = $dbConnection;
        $this->uploadDir = $uploadDir;
    }

    public function changeProfilePicture($userId, $file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return "Upload error.";
        }

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedTypes)) {
            return "Invalid file type.";
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return "File too large.";
        }

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        $newFileName = $userId . '_' . time() . '.' . $extension;
        $targetPath = $this->uploadDir . basename($newFileName);

    if (moveUploadedFile($file['tmp_name'], $targetPath)) {

            $stmt = $this->conn->prepare("UPDATE users SET profile_picture=? WHERE id=?");
            $stmt->bind_param("si", $targetPath, $userId);
            $stmt->execute();
            return "Profile picture updated.";
        }

        return "Upload failed.";
    }
}
?>