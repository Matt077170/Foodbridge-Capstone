<?php
namespace App;
class DonorProfile {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getProfileById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id=? AND role='donor'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

}

?>
