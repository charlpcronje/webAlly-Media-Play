<?php
// File: app/Models/Database.php

namespace html\Models;
use \pdo;

class Database
{
    private static $pdo = null;

    public static function getInstance(): PDO
    {
        if (self::$pdo === null) {
            $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8mb4",
                getenv('DB_HOST') ?: 'localhost',
                getenv('DB_NAME') ?: 'your_database'
            );
            self::$pdo = new \PDO($dsn, getenv('DB_USER') ?: 'root', getenv('DB_PASS') ?: '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => false
            ]);
        }
        return self::$pdo;
    }
}
