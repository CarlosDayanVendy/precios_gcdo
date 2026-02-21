<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'mini_gcdo');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDb() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
            exit;
        }
    }
    return $pdo;
}
