<?php
namespace App\Config;

use Dotenv\Dotenv;

class App
{
    private static array $config = [];

    public static function init(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        self::$config = [
            'app_url' => $_ENV['APP_URL'] ?? 'http://localhost:8888/UnionVote/public',
            'app_env' => $_ENV['APP_ENV'] ?? 'production',
            'app_debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
            'app_timezone' => $_ENV['APP_TIMEZONE'] ?? 'Asia/Ho_Chi_Minh',
            'encryption_key' => $_ENV['APP_ENCRYPTION_KEY'] ?? '',
            'csrf_enabled' => ($_ENV['CSRF_ENABLED'] ?? 'true') === 'true',
            'upload_max_size' => (int)($_ENV['UPLOAD_MAX_SIZE'] ?? 5242880),
            'upload_allowed_extensions' => explode(',', $_ENV['UPLOAD_ALLOWED_EXTENSIONS'] ?? 'xlsx,xls,csv'),
        ];

        date_default_timezone_set(self::$config['app_timezone']);
        mb_internal_encoding('UTF-8');
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$config[$key] ?? $default;
    }

    public static function baseUrl(): string
    {
        return rtrim(self::$config['app_url'], '/');
    }

    public static function isDebug(): bool
    {
        return self::$config['app_debug'];
    }
}
