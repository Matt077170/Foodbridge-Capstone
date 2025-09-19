// tests/LoginTest.php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    public function testValidLogin()
    {
        $login = new Login();
        $result = $login->authenticate('matt@gmail.com', '123123');
        $this->assertTrue($result);
    }

    public function testInvalidLogin()
    {
        $login = new Login();
        $result = $login->authenticate('matt@gmail.com', '123123');
        $this->assertFalse($result);
    }
}
