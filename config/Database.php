<?php
namespace Config;
use PDO;
use PDOException;
require_once __DIR__ . '/env.php';
class Database {
    private static ?PDO $inst = null;
    private function __construct() {}
    public static function getInstance(): PDO {
        if (self::$inst === null) {
            try {
                $dsn = 'mysql:host=' . env('DB_HOST', 'localhost') . ';dbname=' . env('DB_NAME', 'artsync_db') . ';charset=utf8mb4';
                self::$inst = new PDO($dsn, env('DB_USER', 'root'), env('DB_PASS', ''));
                self::$inst->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$inst->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die('Erro DB: ' . $e->getMessage());
            }
        }
        return self::$inst;
    }
    private function __clone() {}
    public function __wakeup() {}
}
