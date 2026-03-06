<?php
namespace App\Core;

class CSRF
{
    public static function generateToken(): string
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function field(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(): bool
    {
        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $sessionToken = Session::get('csrf_token', '');
        if (empty($token) || empty($sessionToken)) {
            return false;
        }
        return hash_equals($sessionToken, $token);
    }

    public static function getToken(): string
    {
        return self::generateToken();
    }
}
