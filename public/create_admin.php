<?php
// File: create_admin.php
require __DIR__ . '/../vendor/autoload.php';

use html\Models\User;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Create admin user
User::create('admin@example.com', 'securepassword', 'admin');

echo "Admin user created successfully.";
