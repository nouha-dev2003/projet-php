<?php
class Config
{
    private static $env = null;

    private static function loadEnv()
    {
        if (self::$env !== null) {
            return self::$env;
        }
        $envFile = __DIR__ . '/../.env';
        if (!file_exists($envFile)) {
            die('.env file not found. Please copy .env.example to .env and configure it.');
        }
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($key, $value) = explode('=', $line, 2);
            self::$env[trim($key)] = trim(trim($value), '"\'');
        }
        return self::$env;
    }

    public static function get($key, $default = null)
    {
        $env = self::loadEnv();
        return $env[$key] ?? $default;
    }

    public static function getDbConfig()
    {
        return [
            'host' => self::get('DB_HOST'),
            'dbname' => self::get('DB_NAME'),
            'user' => self::get('DB_USER'),
            'password' => self::get('DB_PASS'),
            'charset' => 'utf8mb4'
        ];
    }

    public static function getUploadConfig()
    {
        return [
            'max_size' => (int)self::get('UPLOAD_MAX_SIZE', 2097152),
            'allowed_types' => explode(',', self::get('UPLOAD_ALLOWED_TYPES', 'jpg,png,pdf')),
            'upload_dir' => self::get('UPLOAD_DIR', __DIR__ . '/../public/uploads/'),
        ];
    }

    public static function getAppUrl()
    {
        return rtrim(self::get('APP_URL', 'http://localhost:8000'), '/');
    }
}