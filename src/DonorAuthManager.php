<?php

namespace App;

class DonorAuthManager
{
    private bool $loggedIn = true;

    public function logout(): void
    {
        $this->loggedIn = false;
    }

    public function isLoggedIn(): bool
    {
        return $this->loggedIn;
    }
}
