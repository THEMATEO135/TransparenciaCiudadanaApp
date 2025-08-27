<?php
// init_db.php - crea la base de datos SQLite y la tabla initial
$config = require __DIR__ . '/config.php';
$dbPath = $config['db_path'];
$dir = dirname($dbPath);
if (!is_dir($dir)) mkdir($dir, 0755, true);

$db = new PDO('sqlite:' . $dbPath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = <<<SQL
CREATE TABLE IF NOT EXISTS reports (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_name TEXT,
  user_email TEXT,
  user_phone TEXT,
  service_type TEXT,
  description TEXT,
  latitude REAL,
  longitude REAL,
  status TEXT DEFAULT 'nuevo',
  created_at TEXT DEFAULT (datetime('now'))
);
SQL;

$db->exec($sql);
echo "Base de datos inicializada en: $dbPath
";
