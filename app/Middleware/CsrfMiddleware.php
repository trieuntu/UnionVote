<?php
namespace App\Middleware;

use App\Core\CSRF;
use App\Core\Session;

class CsrfMiddleware
{
    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (!CSRF::validate()) {
                http_response_code(403);
                Session::setFlash('error', 'Phiên làm việc đã hết hạn. Vui lòng thử lại.');
                $referer = $_SERVER['HTTP_REFERER'] ?? '/';
                header('Location: ' . $referer);
                exit;
            }
        }
    }
}
