<?php
namespace App\Core;

class View
{
    public static function render(string $viewPath, array $data = []): void
    {
        extract($data);
        $viewFile = dirname(__DIR__) . '/Views/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file not found: {$viewFile}");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        echo $content;
    }

    public static function partial(string $viewPath, array $data = []): void
    {
        extract($data);
        $viewFile = dirname(__DIR__) . '/Views/' . str_replace('.', '/', $viewPath) . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
    }
}
