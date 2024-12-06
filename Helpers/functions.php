<?php
// File: src/Helpers/functions.php

namespace ..\Helpers;

use PDO;

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Get PDO instance
 */
function getPDO(): PDO {
    static $pdo;
    if ($pdo === null) {
        $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8mb4",
            getenv('DB_HOST') ?: 'localhost',
            getenv('DB_NAME') ?: 'your_database'
        );
        $pdo = new PDO($dsn, getenv('DB_USER') ?: 'root', getenv('DB_PASS') ?: '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false
        ]);
    }
    return $pdo;
}

// Additional helper functions can be added here
