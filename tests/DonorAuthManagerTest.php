<?php

use PHPUnit\Framework\TestCase;
use App\DonorAuthManager;

class DonorAuthManagerTest extends TestCase
{
    public function test_donor_user_can_logout_from_dashboard()
    {
        $auth = new DonorAuthManager();

        // Initially logged in
        $this->assertTrue($auth->isLoggedIn(), 'User should be logged in initially.');

        // Perform logout
        $auth->logout();

        // Verify logout
        $this->assertFalse($auth->isLoggedIn(), 'User should be logged out after calling logout.');
    }
}
