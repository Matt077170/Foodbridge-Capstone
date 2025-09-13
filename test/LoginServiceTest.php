<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '../src/LoginService.php';


class LoginServiceTest extends TestCase {
    private $service;

    protected function setUp(): void {
        $this->service = new LoginService();
    }

    public function testValidDonorLogin() {
        $this->assertTrue($this->service->validate('donor@example.com', 'donor123', 'donor'));
    }

    public function testValidOrganizationLogin() {
        $this->assertTrue($this->service->validate('org@example.com', 'org123', 'organization'));
    }

    public function testValidAdminLogin() {
        $this->assertTrue($this->service->validate('admin@example.com', 'admin123', 'admin'));
    }

    public function testInvalidPassword() {
        $this->assertFalse($this->service->validate('donor@example.com', 'wrongpass', 'donor'));
    }

    public function testInvalidRole() {
        $this->assertFalse($this->service->validate('donor@example.com', 'donor123', 'admin'));
    }

    public function testUnknownUser() {
        $this->assertFalse($this->service->validate('unknown@example.com', 'pass', 'donor'));
    }
}
?>