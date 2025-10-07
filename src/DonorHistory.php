<?php

namespace App;
class DonorHistory {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function getDonorHistory($donorId, $keyword = '%', $deliveryMode = '%') {
        $query = "SELECT * FROM donations WHERE donor_id = ? AND caption LIKE ? AND delivery_mode LIKE ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iss", $donorId, $keyword, $deliveryMode);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>