<?php

namespace App;

class UserRegister {
    private $conn;
    private $minPasswordLength = 8;
    
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function register($fullname, $email, $password, $confirmPassword, $role) {
        if (!$this->doPasswordsMatch($password, $confirmPassword)) {
            return false;
        }

        if (!$this->isPasswordLongEnough($password) || !$this->isPasswordStrong($password)) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullname, $email, $hashedPassword, $role);
        return $stmt->execute();
    }

    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function isPasswordStrong($password) {
        return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }

    public function isPasswordLongEnough($password) {
        return strlen($password) >= $this->minPasswordLength;
    }

    public function doPasswordsMatch($password, $confirmPassword) {
        return $password === $confirmPassword;
    }


public function isEmailValid($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

public function hasMissingFields($fullname, $email, $password, $confirmPassword, $role) {
    $fields = compact('fullname', 'email', 'password', 'confirmPassword', 'role');

    foreach ($fields as $key => $value) {
        if (empty(trim($value))) {
            return $key; // Return the name of the missing field
        }
    }

    return false; // All fields are present
}

public function isValidIdUpload($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    return isset($file['tmp_name'], $file['type'], $file['size']) &&
           is_uploaded_file($file['tmp_name']) &&
           in_array($file['type'], $allowedTypes) &&
           $file['size'] <= $maxSize;
}
public function isInvalidIdFormat($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];

    if (!isset($file['type'])) {
        return true; // Missing type info
    }

    return !in_array($file['type'], $allowedTypes);
}

public function isValidUpload($file, $allowedTypes = ['image/jpeg', 'image/png'], $maxSizeMB = 5) {
    return isset($file['tmp_name'], $file['type'], $file['size']) &&
           is_uploaded_file($file['tmp_name']) &&
           in_array($file['type'], $allowedTypes) &&
           $file['size'] <= $maxSizeMB * 1024 * 1024;
}

public function hasMissingUploads($idFile, $selfieFile) {
    if (empty($idFile['tmp_name']) || !is_uploaded_file($idFile['tmp_name'])) {
        return 'ID file is missing or not uploaded';
    }

    if (empty($selfieFile['tmp_name']) || !is_uploaded_file($selfieFile['tmp_name'])) {
        return 'Selfie file is missing or not uploaded';
    }

    return false; // Both files are present
}

}

?>