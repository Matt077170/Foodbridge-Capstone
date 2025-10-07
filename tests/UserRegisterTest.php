<?php
use PHPUnit\Framework\TestCase;
use App\UserRegister;

class UserRegisterTest extends TestCase {
    private $conn;
    private $registration;

    protected function setUp(): void {
        // Real MySQLi connection (or mock if preferred)
        $this->conn = new mysqli('localhost', 'root', '', 'food_donation_db');

        if ($this->conn->connect_error) {
            $this->fail('Database connection failed: ' . $this->conn->connect_error);
        }

        $this->registration = new UserRegister($this->conn);
    }

    public function testSuccessfulRegistration() {
        $result = $this->registration->register('Alice','alice_' . uniqid() . '@example.com','StrongPass123!',
'StrongPass123!', 'donor'
        );
        $this->assertTrue($result);
    }

    public function testPasswordIsHashed() {
        $password = 'StrongPass123!';
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $this->assertTrue(password_verify($password, $hashed));
    }

    public function testEmailExists() {
        $email = 'mattgabrielle12' . uniqid() . '@gmail.com';
   $this->registration->register('Matt Gabrielle Morabe', $email, 'StrongPass123!', 'StrongPass123!', 'donor');

        $exists = $this->registration->emailExists($email);
        $this->assertTrue($exists);
    }

    public function testWeakPasswordFailsValidation() {
        $weakPassword = 'abc123'; // too short, no uppercase, no special char
        $this->assertFalse($this->registration->isPasswordStrong($weakPassword));
    }

    public function testStrongPasswordPassesValidation() {
        $strongPassword = 'StrongPass123!';
        $this->assertTrue($this->registration->isPasswordStrong($strongPassword));
    }

    public function testPasswordTooShortFailsLengthCheck() {
    $shortPassword = 'Ab1!';
    $this->assertFalse($this->registration->isPasswordLongEnough($shortPassword));
}

public function testPasswordLongEnoughPassesLengthCheck() {
    $longPassword = 'Abcdef12!';
    $this->assertTrue($this->registration->isPasswordLongEnough($longPassword));
}

public function testPasswordsDoNotMatch() {
    $this->assertFalse($this->registration->doPasswordsMatch('SecurePass123!', 'Mismatch123!'));
}

public function testPasswordsMatch() {
    $this->assertTrue($this->registration->doPasswordsMatch('SecurePass123!', 'SecurePass123!'));
}

public function testInvalidEmailFormatFailsValidation() {
    $invalidEmail = 'invalid-email@';
    $this->assertFalse($this->registration->isEmailValid($invalidEmail));
}

public function testValidEmailFormatPassesValidation() {
    $validEmail = 'valid.email@example.com';
    $this->assertTrue($this->registration->isEmailValid($validEmail));
}


    protected function tearDown(): void {
        $this->conn->close();
    }

    public function testValidIdUploadPassesValidation() {
    $tempFile = tempnam(sys_get_temp_dir(), 'id');
    file_put_contents($tempFile, 'fake content');

    $file = [
        'tmp_name' => $tempFile,
        'type' => 'image/jpeg',
        'size' => filesize($tempFile),
        'name' => 'test_id.jpg'
    ];

    $this->assertTrue($this->registration->isValidIdUpload($file));
}
    public function testInvalidIdUploadFailsValidation() {
    $file = [
        'tmp_name' => '',
        'type' => 'text/plain',
        'size' => 10 * 1024 * 1024, // 10MB
        'name' => 'test.txt'
    ];

    $this->assertFalse($this->registration->isValidIdUpload($file));
}

public function testInvalidIdFormatFailsValidation() {
    $file = [
        'tmp_name' => tempnam(sys_get_temp_dir(), 'id'),
        'type' => 'text/plain', // unsupported format
        'size' => 1024,
        'name' => 'id.txt'
    ];

    $this->assertTrue($this->registration->isInvalidIdFormat($file));
}

public function testValidIdFormatPassesValidation() {
    $file = [
        'tmp_name' => tempnam(sys_get_temp_dir(), 'id'),
        'type' => 'image/jpeg',
        'size' => 1024,
        'name' => 'id.jpg'
    ];

    $this->assertFalse($this->registration->isInvalidIdFormat($file));
}

public function testValidIdAndSelfieUploadPassesValidation() {
    $tempId = tempnam(sys_get_temp_dir(), 'id');
    $tempSelfie = tempnam(sys_get_temp_dir(), 'selfie');
    file_put_contents($tempId, 'fake id');
    file_put_contents($tempSelfie, 'fake selfie');

    $idFile = [
        'tmp_name' => $tempId,
        'type' => 'image/jpeg',
        'size' => filesize($tempId),
        'name' => 'id.jpg'
    ];

    $selfieFile = [
        'tmp_name' => $tempSelfie,
        'type' => 'image/jpeg',
        'size' => filesize($tempSelfie),
        'name' => 'selfie.jpg'
    ];

    $this->assertTrue($this->registration->isValidUpload($idFile));
    $this->assertTrue($this->registration->isValidUpload($selfieFile));
}

public function testMissingIdUploadFailsValidation() {
    $selfieFile = [
        'tmp_name' => tempnam(sys_get_temp_dir(), 'selfie'),
        'type' => 'image/jpeg',
        'size' => 1024
    ];

    $missing = $this->registration->hasMissingUploads([], $selfieFile);
    $this->assertEquals('ID file is missing or not uploaded', $missing);
}

public function testMissingSelfieUploadFailsValidation() {
    $idFile = [
        'tmp_name' => tempnam(sys_get_temp_dir(), 'id'),
        'type' => 'image/jpeg',
        'size' => 1024
    ];

    $missing = $this->registration->hasMissingUploads($idFile, []);
    $this->assertEquals('Selfie file is missing or not uploaded', $missing);
}

public function testBothUploadsPresentPassValidation() {
    $idFile = [
        'tmp_name' => tempnam(sys_get_temp_dir(), 'id'),
        'type' => 'image/jpeg',
        'size' => 1024
    ];

    $selfieFile = [
        'tmp_name' => tempnam(sys_get_temp_dir(), 'selfie'),
        'type' => 'image/jpeg',
        'size' => 1024
    ];

    $missing = $this->registration->hasMissingUploads($idFile, $selfieFile);
    $this->assertFalse($missing);
}

}
?>