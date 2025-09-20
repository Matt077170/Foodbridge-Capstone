<?php
namespace App;

class CreateDonorService {
    public static function isDonor(array $session): bool {
        return isset($session['user']) && $session['user']['role'] === 'donor';
    }
}
