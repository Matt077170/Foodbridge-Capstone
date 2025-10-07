<?php

class UserRepository {
    private $pdo;

    public function __construct($dsn, $username, $password) {
        $this->pdo = new PDO($dsn, $username, $password);
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>