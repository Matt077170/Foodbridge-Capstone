<?php
use PHPUnit\Framework\TestCase;
use App\DonorProfile;

class DonorProfileTest extends TestCase {
    private $mockConn;
    private $donorProfile;

    protected function setUp(): void {
        // Create a mock mysqli connection
        $this->mockConn = $this->createMock(mysqli::class);
        $this->donorProfile = new DonorProfile($this->mockConn);
    }

    public function testGetProfileByIdReturnsCorrectData() {
        $expectedData = [
            'id' => 1,
            'fullname' => 'Matt Gabrielle Morabe',
            'address' => 'Manila',
            'contact' => '09196629210',
            'email' => 'mattgabrielle12@gmail.com',
            'role' => 'donor',
            'profile_picture' => 'uploads/profile_pictures/1_1634567890.jpg'
        ];

        // Mock the statement and result
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockResult = $this->createMock(mysqli_result::class);

        $mockResult->method('fetch_assoc')->willReturn($expectedData);
        $mockStmt->method('get_result')->willReturn($mockResult);

        $this->mockConn->method('prepare')->willReturn($mockStmt);

        $result = $this->donorProfile->getProfileById(1);
        $this->assertEquals($expectedData, $result);
    }
}
