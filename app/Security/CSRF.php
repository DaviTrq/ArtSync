<?php
namespace App\Security;
class CSRF {
    public static function generateToken(): string {
        !isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
    public static function validateToken(string $token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    public static function getTokenField(): string {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::generateToken(), ENT_QUOTES, 'UTF-8') . '">';
    }
}
