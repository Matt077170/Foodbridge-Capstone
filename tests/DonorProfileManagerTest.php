<?php
use PHPUnit\Framework\TestCase;
use App\DonorProfileManager;
function moveUploadedFile($source, $destination) {
    return true; // simulate success
}

class DonorProfileManagerTest extends TestCase {
    private $mockConn;
    private $manager;

    protected function setUp(): void {
        $this->mockConn = $this->createMock(mysqli::class);
        $this->manager = new DonorProfileManager($this->mockConn, '/tmp/test_uploads/');
    }

    public function testChangeProfilePictureSuccess() {
        $file = [
            'name' => 'busss.png',
            'type' => 'image/png',
            'tmp_name' => tempnam(sys_get_temp_dir(), 'test'),
            'error' => UPLOAD_ERR_OK,
            'size' => 400000
        ];
        file_put_contents($file['tmp_name'], 'fake image content');

        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('execute')->willReturn(true);
        $this->mockConn->method('prepare')->willReturn($mockStmt);

        $result = $this->manager->changeProfilePicture(1, $file);
        $this->assertEquals("Profile picture updated.", $result);
    }

    public function testChangeProfilePictureInvalidType() {
        $file = [
            'name' => 'malware.exe',
            'type' => 'application/octet-stream',
            'tmp_name' => tempnam(sys_get_temp_dir(), 'test'),
            'error' => UPLOAD_ERR_OK,
            'size' => 400000
        ];

        $result = $this->manager->changeProfilePicture(1, $file);
        $this->assertEquals("Invalid file type.", $result);
    }

    public function testChangeProfilePictureTooLarge() {
        $file = [
            'name' => 'big.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => tempnam(sys_get_temp_dir(), 'test'),
            'error' => UPLOAD_ERR_OK,
            'size' => 6000000
        ];

        $result = $this->manager->changeProfilePicture(1, $file);
        $this->assertEquals("File too large.", $result);
    }

    public function testChangeProfilePictureUploadError() {
        $file = [
            'name' => 'profile.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0
        ];

        $result = $this->manager->changeProfilePicture(1, $file);
        $this->assertEquals("Upload error.", $result);
    }
}
?>