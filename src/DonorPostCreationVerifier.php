<?php
namespace App;

class DonorPostCreationVerifier {
    /**
     * Verifies if a post was successfully created.
     *
     * @param bool $dbResult Result of the database insert operation
     * @param array $postData Must contain: user_id, description, featured_image (optional)
     * @return array ['success' => bool, 'message' => string]
     */
    public static function verify(bool $dbResult, array $postData): array {
        if (!$dbResult) {
            return ['success' => false, 'message' => 'Database insert failed'];
        }

        if (!isset($postData['user_id']) || !is_numeric($postData['user_id']) || $postData['user_id'] <= 0) {
            return ['success' => false, 'message' => 'Invalid user ID'];
        }

        if (!isset($postData['description']) || strlen(trim($postData['description'])) < 10) {
            return ['success' => false, 'message' => 'Description is too short or missing'];
        }

        if (isset($postData['featured_image']) && !empty($postData['featured_image'])) {
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($postData['featured_image'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedTypes)) {
                return ['success' => false, 'message' => 'Invalid image type'];
            }
        }

        return ['success' => true, 'message' => 'Post created successfully'];
    }
}
