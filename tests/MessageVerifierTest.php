<?php
use PHPUnit\Framework\TestCase;
use App\MessageVerifier;

class MessageVerifierTest extends TestCase {
    public function testValidMessage() {
        $message = [
            'sender_role' => 'organization',
            'recipient_id' => 1,
            'sender_id' => 2,
            'message_content' => 'Thank you for your donation!',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = MessageVerifier::verify($message);
        $this->assertTrue($result['verified']);
        $this->assertNull($result['reason']);
    }

    public function testMissingField() {
        $message = [
            'sender_role' => 'organization',
            'recipient_id' => 1,
            'message_content' => 'Hello!',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = MessageVerifier::verify($message);
        $this->assertFalse($result['verified']);
        $this->assertEquals('Missing field: sender_id', $result['reason']);
    }

    public function testInvalidRole() {
        $message = [
            'sender_role' => 'donor',
            'recipient_id' => 1,
            'sender_id' => 2,
            'message_content' => 'Hello!',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = MessageVerifier::verify($message);
        $this->assertFalse($result['verified']);
        $this->assertEquals('Sender is not an organization', $result['reason']);
    }

    public function testShortContent() {
        $message = [
            'sender_role' => 'organization',
            'recipient_id' => 1,
            'sender_id' => 2,
            'message_content' => 'Hi',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = MessageVerifier::verify($message);
        $this->assertFalse($result['verified']);
        $this->assertEquals('Message content too short', $result['reason']);
    }

    public function testOldMessage() {
        $message = [
            'sender_role' => 'organization',
            'recipient_id' => 1,
            'sender_id' => 2,
            'message_content' => 'We appreciate your support.',
            'created_at' => date('Y-m-d H:i:s', strtotime('-45 days'))
        ];

        $result = MessageVerifier::verify($message);
        $this->assertFalse($result['verified']);
        $this->assertEquals('Message is older than 30 days', $result['reason']);
    }
}
?>