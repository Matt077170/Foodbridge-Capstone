<?php
use App\CreateDonorService;
use PHPUnit\Framework\TestCase;


class CreateDonorServiceTest extends TestCase {

    public function testIsDonorReturnsTrueForValidDonor() {
        $session = ['user' => ['role' => 'donor']];
        $this->assertTrue(CreateDonorService::isDonor($session));
    }

    public function testIsDonorReturnsFalseForMissingUser() {
        $session = [];
        $this->assertFalse(CreateDonorService::isDonor($session));
    }

    public function testIsDonorReturnsFalseForWrongRole() {
        $session = ['user' => ['role' => 'admin']];
        $this->assertFalse(CreateDonorService::isDonor($session));
    }
}
?>