<?php

use App\LoginService;
use PHPUnit\Framework\TestCase;

// The test class must extend PHPUnit\Framework\TestCase to inherit all of PHPUnit's functionality.
class LoginServiceTest extends TestCase
{
    /**
     * @var LoginService The instance of the class we are testing.
     */
    private LoginService $LoginService;

    /**
     * The setUp() method is a test fixture method that runs before each test.
     * It ensures each test starts with a clean instance of the LoginService.
     */
    protected function setUp(): void
    {
        $this->LoginService = new LoginService();
    }

    /**
     * A test to verify a successful login with valid credentials.
     * The method name should be descriptive.
     */
    public function testValidLogin(): void
    {
        // Act: Call the method we want to test.
        $result = $this->LoginService->authorization('matt@gmail.com', '123123');

        // Assert: Check if the result is what we expect.
        $this->assertTrue($result);
    }

    /**
     * A test to verify that a login fails with an incorrect password.
     */

    public function testInvalidEmail(): void
    {
        // Act
        $result = $this->LoginService->authorization('m@gmail.com', '123123');

        // Assert
        $this->assertFalse($result);
    }

    public function testInvalidPassword(): void
    {
        // Act
        $result = $this->LoginService->authorization('matt@gmail.com', '123456');

        // Assert
        $this->assertFalse($result);
    }

    /**
     * A test to verify that a login fails for a user that does not exist.
     */
    public function testNonExistentUser(): void
    {
        // Act
        $result = $this->LoginService->authorization('donor@example.com', 'password12345');

        // Assert
        $this->assertFalse($result);
    }
    
     public function testInvalidEmailPassword(): void
    {
        // Act
        $result = $this->LoginService->authorization('donor@example.com', 'password1234567');

        // Assert
        $this->assertFalse($result);
    }
    public function testEmptyCredentials(): void
    {
        // Act
        $result = $this->LoginService->authorization('', '');

        // Assert
        $this->assertFalse($result);
    }


public function testForgotPassword(): void
{
    // Act
    $token = $this->LoginService->forgotPassword('matt@gmail.com');

    // Assert
    $this->assertNotNull($token);
    $this->assertIsString($token);
    $this->assertEquals(32, strlen($token)); // 16 bytes = 32 hex characters
}

public function testForgotPasswordInvalidEmail(): void
{
    $token = $this->LoginService->forgotPassword('unknown@example.com');
    $this->assertNull($token);
}

}

?>