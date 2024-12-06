<?php
// File: app/Models/User.php

namespace html\Models;
use \PDO;

class User
{
    public $id;
    public $email;
    public $password_hash;
    public $role;
    public $created_at;
    public $updated_at;

    /**
     * Find user by email
     */
    public static function findByEmail($email)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $user = new User();
            foreach ($data as $key => $value) {
                $user->$key = $value;
            }
            return $user;
        }
        return null;
    }

    /**
     * Create a new user
     */
    public static function create($email, $password, $role = 'admin')
    {
        $pdo = Database::getInstance();
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role) VALUES (:email, :password_hash, :role)");
        $stmt->execute([
            ':email' => $email,
            ':password_hash' => $passwordHash,
            ':role' => $role
        ]);
    }
}
