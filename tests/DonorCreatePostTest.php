<?php
use PHPUnit\Framework\TestCase;
use App\DonorCreatePost;

class DonorCreatePostTest extends TestCase {
    public function testValidPost() {
        $post = [
            'user_id' => 1,
            'description' => 'This is a valid post description.',
            'featured_image' => 'image.jpg',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = DonorCreatePost::validate($post);
        $this->assertTrue($result['valid']);
        $this->assertNull($result['reason']);
    }

    public function testMissingUserId() {
        $post = [
            'description' => 'Valid description',
            'featured_image' => 'image.jpg'
        ];

        $result = DonorCreatePost::validate($post);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Missing field: user_id', $result['reason']);
    }

    public function testShortDescription() {
        $post = [
            'user_id' => 1,
            'description' => 'Short'
        ];

        $result = DonorCreatePost::validate($post);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Description is too short', $result['reason']);
    }

    public function testInvalidImageType() {
        $post = [
            'user_id' => 1,
            'description' => 'Valid description',
            'featured_image' => 'image.exe'
        ];

        $result = DonorCreatePost::validate($post);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Invalid image type', $result['reason']);
    }

    public function testFutureTimestamp() {
        $post = [
            'user_id' => 1,
            'description' => 'Valid description',
            'created_at' => date('Y-m-d H:i:s', strtotime('+1 day'))
        ];

        $result = DonorCreatePost::validate($post);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Post creation time is in the future', $result['reason']);
    }

    public function testInvalidTimestampFormat() {
        $post = [
            'user_id' => 1,
            'description' => 'Valid description',
            'created_at' => 'not-a-date'
        ];

        $result = DonorCreatePost::validate($post);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Invalid created_at format', $result['reason']);
    }
}
