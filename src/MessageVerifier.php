<?php
namespace App;

class MessageVerifier {
    /**
     * Verifies if a message is from a valid organization.
     *
     * @param array $message Must contain: sender_role, recipient_id, sender_id, message_content, created_at
     * @return array ['verified' => bool, 'reason' => string|null]
     */
    public static function verify(array $message): array {
        $required = ['sender_role', 'recipient_id', 'sender_id', 'message_content', 'created_at'];

        foreach ($required as $key) {
            if (!array_key_exists($key, $message)) {
                return ['verified' => false, 'reason' => "Missing field: $key"];
            }
        }

        if ($message['sender_role'] !== 'organization') {
            return ['verified' => false, 'reason' => 'Sender is not an organization'];
        }

        if (strlen(trim($message['message_content'])) < 5) {
            return ['verified' => false, 'reason' => 'Message content too short'];
        }

        $timestamp = strtotime($message['created_at']);
        if ($timestamp < strtotime('-30 days')) {
            return ['verified' => false, 'reason' => 'Message is older than 30 days'];
        }

        return ['verified' => true, 'reason' => null];
    }
}
