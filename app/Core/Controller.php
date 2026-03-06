<?php
namespace App\Core;

class Controller
{
    protected function view(string $viewPath, array $data = []): void
    {
        View::render($viewPath, $data);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function redirectBack(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? \App\Config\App::baseUrl();
        header('Location: ' . $referer);
        exit;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function setFlash(string $type, string $message): void
    {
        Session::setFlash($type, $message);
    }
}
