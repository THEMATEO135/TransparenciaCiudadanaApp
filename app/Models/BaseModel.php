<?php
namespace App\Models;


class BaseModel {
protected static $pdo;


public function __construct() {
if (!self::$pdo) {
$config = require __DIR__ . '/../../config/config.php';
$db = $config['db'];
$dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s;options='--client_encoding=%s'",
$db['host'], $db['port'], $db['database'], $db['charset']
);


try {
self::$pdo = new \PDO($dsn, $db['username'], $db['password'], [
\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
]);
} catch (\PDOException $e) {
// Manejo de error centralizado (log + lanzar excepción ligera)
throw new \Exception('DB connection error: ' . $e->getMessage());
}
}
}


protected function pdo() {
return self::$pdo;
}
}