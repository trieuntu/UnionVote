<?php
namespace App\Core;

class Auth
{
    public static function login(array $user): void
    {
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('user_name', $user['full_name']);
        Session::set('user_username', $user['username']);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::has('user_id');
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return [
            'id' => Session::get('user_id'),
            'role' => Session::get('user_role'),
            'full_name' => Session::get('user_name'),
            'username' => Session::get('user_username'),
        ];
    }

    public static function userId(): ?int
    {
        return Session::get('user_id');
    }

    public static function isAdmin(): bool
    {
        return Session::get('user_role') === 'admin';
    }

    public static function isEditor(): bool
    {
        return Session::get('user_role') === 'editor';
    }
}
