<?php

namespace App;

/**
 * The LoginService class is responsible for authenticating a user.
 */
class LoginService
{
    /**
     * @var array A simple, hardcoded array of user credentials for demonstration.
     * In a real application, this data would come from a database.
     */
    private $users = [
        'matt@gmail.com' => '123123'
        
    ];

    /**
     * Authenticates a user based on their email and password.
     *
     * @param string $email The user's email address.
     * @param string $password The user's password.
     * @return bool True if the credentials are valid, false otherwise.
     */
    public function authorization(string $email, string $password): bool
    {
        // First, check if the provided email exists in our user list.
        if (!isset($this->users[$email])) {
            return false;
        }

        // If the email exists, compare the provided password with the stored password.
        // NOTE: In a real-world application, you would use a function like password_verify()
        // to securely check against a hashed password.
        return $this->users[$email] === $password;
    }

  public function forgotPassword(string $email): ?string
    {
        if (!isset($this->users[$email])) {
            return null; // User not found
        }

        // Generate a secure token
        $token = bin2hex(random_bytes(16));

        return $token; // In a real application, you would store this token in the database
    }
    
}
