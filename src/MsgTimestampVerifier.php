<?php
namespace App;

class MsgTimestampVerifier {
    /**
     * Verifies if a timestamp is valid, not in the future, and within a given age limit.
     *
     * @param string $timestamp Format: Y-m-d H:i:s
     * @param int $maxAgeDays Maximum age in days (default: 30)
     * @return array ['valid' => bool, 'reason' => string|null]
     */
    public static function verify(string $timestamp, int $maxAgeDays = 30): array {
        $messageTime = strtotime($timestamp);
        if (!$messageTime) {
            return ['valid' => false, 'reason' => 'Invalid timestamp format'];
        }

        if ($messageTime > time()) {
            return ['valid' => false, 'reason' => 'Timestamp is in the future'];
        }

        $ageLimit = strtotime("-{$maxAgeDays} days");
        if ($messageTime < $ageLimit) {
            return ['valid' => false, 'reason' => "Message is older than {$maxAgeDays} days"];
        }

        return ['valid' => true, 'reason' => null];
    }
}
