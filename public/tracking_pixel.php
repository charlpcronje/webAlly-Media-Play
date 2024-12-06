<?php
// File: public/tracking_pixel.php

/**
 * Tracking Pixel Handler with Multiple Pixels Support
 * 
 * This script:
 * - Serves tracking pixels with controlled download speeds.
 * - Ensures only the latest tracking pixel per unique `tracking_id` is active.
 * - Allows multiple tracking pixels per page, each identified by a unique name.
 */
require "autoload_psr4.php"


// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Start session
session_start();

// Database connection (PDO)
function getPDOProxy() {
    static $pdo;
    if ($pdo === null) {
        $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8mb4",
            $_ENV['DB_HOST'] ?? 'localhost',
            $_ENV['DB_NAME'] ?? 'your_database'
        );
        $pdo = new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false
        ]);
    }
    return $pdo;
}

// Helper function to sanitize inputs
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Retrieve and sanitize GET parameters
$ext = sanitize($_GET['ext'] ?? 'gif');
$page_id = sanitize($_GET['page_id'] ?? null);
$tracking_id = sanitize($_GET['tracking_id'] ?? null);

// Validate required parameters
if (!$page_id || !$tracking_id) {
    http_response_code(400);
    echo "Bad Request: Missing parameters.";
    exit;
}

// Identify the client
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

// Database interactions
$pdo = getPDOProxy();

// Check and validate the tracking_id
$stmt = $pdo->prepare("SELECT tracking_id FROM tracking_pixel_sessions WHERE tracking_id = :tracking_id AND page_id = :page_id LIMIT 1");
$stmt->execute([':tracking_id' => $tracking_id, ':page_id' => $page_id]);
$valid = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$valid) {
    // Invalid or outdated tracking_id; terminate the connection early
    http_response_code(204); // No Content
    exit;
}

// Fetch tracking pixel configuration
$stmt = $pdo->prepare("SELECT image_path, download_speed FROM tracking_pixel_configs WHERE page_id = :page_id AND name = (SELECT name FROM tracking_pixel_configs WHERE tracking_id = :tracking_id AND page_id = :page_id LIMIT 1) LIMIT 1");
$stmt->execute([':page_id' => $page_id, ':tracking_id' => $tracking_id]);
$config = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$config) {
    // No configuration found for this tracking_id
    http_response_code(404);
    echo "Tracking pixel not configured for this page.";
    exit;
}

// Determine download speed
$download_speed = (int)$config['download_speed']; // Bytes per second
if ($download_speed < 0) $download_speed = 0; // Ensure non-negative

// Determine image path
$image_path = __DIR__ . '/' . ltrim($config['image_path'], '/');

// Validate image file
if (!file_exists($image_path)) {
    http_response_code(404);
    echo "Tracking pixel image not found.";
    exit;
}

// Set appropriate headers
$mimeType = mime_content_type($image_path);
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($image_path));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Disable buffering
@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', false);
while (ob_get_level()) ob_end_clean();

// Read and stream the image with controlled speed
$chunkSize = 1024; // 1KB chunks
$handle = fopen($image_path, 'rb');
if ($handle === false) {
    http_response_code(500);
    echo "Internal Server Error: Unable to read image.";
    exit;
}

if ($download_speed === 0) {
    // No speed limit; stream the image normally
    while (!feof($handle)) {
        $data = fread($handle, $chunkSize);
        echo $data;
        flush();
    }
} else {
    // Stream with limited download speed
    while (!feof($handle)) {
        $data = fread($handle, $download_speed); // Read bytes equal to download_speed
        echo $data;
        flush();
        // Sleep for 1 second to limit to download_speed bytes per second
        sleep(1);
    }
}

fclose($handle);
exit;
?>
