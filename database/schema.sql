-- File: database/schema.sql

CREATE DATABASE IF NOT EXISTS your_database;
USE your_database;

-- Users table for admin authentication
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) DEFAULT 'admin',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Pages table
CREATE TABLE `pages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `is_archived` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Media table
CREATE TABLE `media` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `page_id` INT DEFAULT NULL,
  `file_path` VARCHAR(1024) NOT NULL,
  `media_type` VARCHAR(50), -- e.g., image, video, audio
  `props` JSON DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE SET NULL
);

-- Sessions table
CREATE TABLE `sessions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ip_address` VARCHAR(45) NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NOT NULL,
  `is_filtered` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Media Logs table
CREATE TABLE `media_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_id` INT NOT NULL,
  `media_id` INT NOT NULL,
  `action` VARCHAR(50) NOT NULL, -- e.g., play, pause, progress
  `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `current_time` INT DEFAULT 0, -- For videos/audio: seconds watched/listened
  `user_agent` TEXT,
  `is_whatsapp` TINYINT(1) DEFAULT 0,
  FOREIGN KEY (`session_id`) REFERENCES `sessions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE CASCADE
);

-- IPs table for managing IP addresses
CREATE TABLE `ips` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ip_address` VARCHAR(45) UNIQUE NOT NULL,
  `name` VARCHAR(255) DEFAULT NULL,
  `is_filtered` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tracking Pixel Configurations
CREATE TABLE `tracking_pixel_configs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `page_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `image_path` VARCHAR(1024) NOT NULL,
  `download_speed` INT NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_pixel_per_page` (`page_id`, `name`)
);

-- Tracking Pixel Sessions
CREATE TABLE `tracking_pixel_sessions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tracking_id` VARCHAR(255) UNIQUE NOT NULL,
  `tracking_pixel_id` INT NOT NULL,
  `page_id` INT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`tracking_pixel_id`) REFERENCES `tracking_pixel_configs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE
);

-- Page Views table
CREATE TABLE `page_views` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `page_id` INT NOT NULL,
  `session_id` INT NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NOT NULL,
  `duration_seconds` INT NOT NULL,
  FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`session_id`) REFERENCES `sessions`(`id`) ON DELETE CASCADE
);
