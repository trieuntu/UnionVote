<?php
namespace App\Middleware;

use App\Core\Auth;
use App\Config\App;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            header('Location: ' . App::baseUrl() . '/admin/login');
            exit;
        }
    }
}
