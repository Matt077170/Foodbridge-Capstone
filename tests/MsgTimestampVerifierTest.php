<?php
use PHPUnit\Framework\TestCase;
use App\MsgTimestampVerifier;

class MsgTimestampVerifierTest extends TestCase {
    public function testValidTimestamp() {
        $timestamp = date('Y-m-d H:i:s');
        $result = MsgTimestampVerifier::verify($timestamp);
        $this->assertTrue($result['valid']);
        $this->assertNull($result['reason']);
    }

    public function testFutureTimestamp() {
        $timestamp = date('Y-m-d H:i:s', strtotime('+1 day'));
        $result = MsgTimestampVerifier::verify($timestamp);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Timestamp is in the future', $result['reason']);
    }

    public function testOldTimestamp() {
        $timestamp = date('Y-m-d H:i:s', strtotime('-45 days'));
        $result = MsgTimestampVerifier::verify($timestamp, 30);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Message is older than 30 days', $result['reason']);
    }

    public function testInvalidFormat() {
        $timestamp = 'not-a-date';
        $result = MsgTimestampVerifier::verify($timestamp);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Invalid timestamp format', $result['reason']);
    }
}
