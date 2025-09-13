<?php
// LoginService.php
class LoginService {
    private $users;

    public function __construct() {
        $this->users = [
            'donor@example.com' => ['password' => 'donor123', 'role' => 'donor'],
            'org@example.com' => ['password' => 'org123', 'role' => 'organization'],
            'admin@example.com' => ['password' => 'admin123', 'role' => 'admin'],
        ];
    }

    public function validate($email, $password, $role) {
        if (!isset($this->users[$email])) return false;
        $user = $this->users[$email];
        return $user['password'] === $password && $user['role'] === $role;
    }
}
?>