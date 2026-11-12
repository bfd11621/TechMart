<?php
/**
 * Automatically create irack_db database if it does not exist before migrations.
 */

$envFile = __DIR__ . '/.env';
$dbHost = '127.0.0.1';
$dbPort = 3306;
$dbDatabase = 'irack_db';
$dbUsername = 'root';
$dbPassword = '';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if ($key === 'DB_HOST') $dbHost = $value;
            if ($key === 'DB_PORT') $dbPort = (int)$value;
            if ($key === 'DB_DATABASE') $dbDatabase = $value;
            if ($key === 'DB_USERNAME') $dbUsername = $value;
            if ($key === 'DB_PASSWORD') $dbPassword = $value;
        }
    }
}

try {
    echo "[*] Connecting to MySQL server at {$dbHost}:{$dbPort}...\n";
    $dsn = "mysql:host={$dbHost};port={$dbPort}";
    $pdo = new PDO($dsn, $dbUsername, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "[*] Ensuring database '{$dbDatabase}' exists...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbDatabase}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "[OK] Database '{$dbDatabase}' is ready for migrations.\n";
} catch (PDOException $e) {
    echo "[WARNING] Could not automatically create database: " . $e->getMessage() . "\n";
    echo "If MySQL is running in XAMPP, please create database '{$dbDatabase}' manually in phpMyAdmin.\n";
}
