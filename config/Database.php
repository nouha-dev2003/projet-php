<?php
namespace Config;

class Database
{
    private static $pdo = null;

    public static function getInstance()
    {
        if (self::$pdo === null) {
            try {
                require_once __DIR__ . '/Config.php'; // ensure Config is loaded
                $dbConfig = \Config::getDbConfig();
                $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
                self::$pdo = new \PDO($dsn, $dbConfig['user'], $dbConfig['password']);
                self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                // Log the real exception internally, but throw a generic one to avoid leaking credentials
                error_log("Database Connection Error: " . $e->getMessage());
                throw new \RuntimeException("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    private function __construct() {}
    private function __clone() {}
    public function __wakeup() {
        throw new \LogicException("Cannot unserialize a singleton.");
    }
}