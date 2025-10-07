<?php
use PHPUnit\Framework\TestCase;
use App\DonorPostCreationVerifier;

class DonorPostCreationVerifierTest extends TestCase {
    public function testSuccessfulPostCreation() {
        $postData = [
            'user_id' => 1,
            'description' => 'This is a valid post description.',
            'featured_image' => 'photo.jpg'
        ];

        $result = DonorPostCreationVerifier::verify(true, $postData);
        $this->assertTrue($result['success']);
        $this->assertEquals('Post created successfully', $result['message']);
    }

    public function testDatabaseFailure() {
        $postData = [
            'user_id' => 1,
            'description' => 'Valid description',
            'featured_image' => 'photo.jpg'
        ];

        $result = DonorPostCreationVerifier::verify(false, $postData);
        $this->assertFalse($result['success']);
        $this->assertEquals('Database insert failed', $result['message']);
    }

    public function testMissingUserId() {
        $postData = [
            'description' => 'Valid description',
            'featured_image' => 'photo.jpg'
        ];

        $result = DonorPostCreationVerifier::verify(true, $postData);
        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid user ID', $result['message']);
    }

    public function testShortDescription() {
        $postData = [
            'user_id' => 1,
            'description' => 'Short',
            'featured_image' => 'photo.jpg'
        ];

        $result = DonorPostCreationVerifier::verify(true, $postData);
        $this->assertFalse($result['success']);
        $this->assertEquals('Description is too short or missing', $result['message']);
    }

    public function testInvalidImageType() {
        $postData = [
            'user_id' => 1,
            'description' => 'Valid description',
            'featured_image' => 'malware.exe'
        ];

        $result = DonorPostCreationVerifier::verify(true, $postData);
        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid image type', $result['message']);
    }
}
