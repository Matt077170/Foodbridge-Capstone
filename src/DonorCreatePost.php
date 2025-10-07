<?php
namespace App;

class DonorCreatePost {
    /**
     * Validates a post's fields.
     *
     * @param array $post Must contain: user_id, description, featured_image (optional), created_at (optional)
     * @return array ['valid' => bool, 'reason' => string|null]
     */
    public static function validate(array $post): array {
        $required = ['user_id', 'description'];

        foreach ($required as $field) {
            if (!array_key_exists($field, $post)) {
                return ['valid' => false, 'reason' => "Missing field: $field"];
            }
        }

        if (!is_numeric($post['user_id']) || $post['user_id'] <= 0) {
            return ['valid' => false, 'reason' => 'Invalid user ID'];
        }

        if (strlen(trim($post['description'])) < 10) {
            return ['valid' => false, 'reason' => 'Description is too short'];
        }

        if (isset($post['featured_image']) && !empty($post['featured_image'])) {
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($post['featured_image'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedTypes)) {
                return ['valid' => false, 'reason' => 'Invalid image type'];
            }
        }

        if (isset($post['created_at'])) {
            $timestamp = strtotime($post['created_at']);
            if (!$timestamp) {
                return ['valid' => false, 'reason' => 'Invalid created_at format'];
            }
            if ($timestamp > time()) {
                return ['valid' => false, 'reason' => 'Post creation time is in the future'];
            }
        }

        return ['valid' => true, 'reason' => null];
    }
}
