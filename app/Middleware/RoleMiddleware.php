<?php
namespace App\Middleware;

use App\Core\Auth;
use App\Config\App;

class RoleMiddleware
{
    public function handle(): void
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            echo '<h1>403 - Không có quyền truy cập</h1>';
            exit;
        }
    }
}
