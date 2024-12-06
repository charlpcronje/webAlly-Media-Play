<?php
// File: public/proxy.php

/**
 * Media Proxy Script
 * 
 * This script:
 * - Receives requests for media files via rewrite rules.
 * - Identifies the requested file and checks if it exists.
 * - Logs the request (and interaction) to the database.
 * - Tracks sessions by IP, user agent, and sets a flag if WhatsApp is detected.
 * - Serves the file content.
 * 
 * Requirements:
 * - Same DB setup as the admin code.
 * - Session logic for IP-based sessions.
 * - Additional columns in media_logs: user_agent (TEXT), is_whatsapp (TINYINT).
 * 
 * .htaccess rules:
 * RewriteEngine On
 * RewriteCond %{REQUEST_URI} \.(mp3|mp4|png|jpg|jpeg|gif|webp|avi|wav)$ [NC]
 * RewriteRule ^media/(.*)$ proxy.php?file=$1 [L,QSA]
 */

require __DIR__ . '/../vendor/autoload.php';

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

/**
 * Get or create a session for the given IP address. 
 * If last session ended more than 30 min ago, create a new one.
 */
function getOrCreateSession($ip) {
    $pdo = getPDOProxy();
    // Check if a current session exists (last end_time)
    $stmt = $pdo->prepare("SELECT * FROM sessions WHERE ip_address = :ip_address ORDER BY id DESC LIMIT 1");
    $stmt->execute([':ip_address' => $ip]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    $now = time();

    if ($session) {
        // If session end_time is within last 30 minutes, continue it
        $endTime = strtotime($session['end_time']);
        if (($now - $endTime) < (30 * 60)) {
            // Update end_time to now
            $update = $pdo->prepare("UPDATE sessions SET end_time = NOW() WHERE id = :id");
            $update->execute([':id' => $session['id']]);
            return $session['id'];
        }
    }

    // Create new session
    $insert = $pdo->prepare("INSERT INTO sessions (ip_address, start_time, end_time, created_at, updated_at) VALUES (:ip, NOW(), NOW(), NOW(), NOW())");
    $insert->execute([':ip' => $ip]);
    return (int)$pdo->lastInsertId();
}

/**
 * Find media record by file name (the stored media file name).
 */
function findMediaByFilePath($fileName) {
    $pdo = getPDOProxy();
    $stmt = $pdo->prepare("SELECT * FROM media WHERE file_path LIKE :filePath LIMIT 1");
    $stmt->execute([':filePath' => "%$fileName"]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get the requested file
$file = $_GET['file'] ?? null;
if (!$file) {
    http_response_code(400);
    echo "Bad Request: No file specified.";
    exit;
}

$filePath = __DIR__ . '/uploads/' . basename($file);
if (!file_exists($filePath)) {
    http_response_code(404);
    echo "File not found.";
    exit;
}

// Identify the client
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

// Determine if WhatsApp is involved
$isWhatsApp = (stripos($userAgent, 'WhatsApp') !== false) ? 1 : 0;

// Session management
$sessionId = getOrCreateSession($ipAddress);

// Find media entry
$mediaRecord = findMediaByFilePath($file);
$mediaId = $mediaRecord ? $mediaRecord['id'] : null;
$pageId = $mediaRecord ? $mediaRecord['page_id'] : null;

// Log the media access
$pdo = getPDOProxy();

// Determine action based on media type
$mediaType = strtolower($mediaRecord['media_type'] ?? '');
switch ($mediaType) {
    case 'image':
        $action = 'view';
        break;
    case 'video':
    case 'audio':
        $action = 'access';
        break;
    default:
        $action = 'access';
        break;
}

$insertLog = $pdo->prepare("INSERT INTO media_logs (session_id, media_id, action, timestamp, current_time, user_agent, is_whatsapp) VALUES (:session_id, :media_id, :action, NOW(), 0, :user_agent, :is_whatsapp)");
$insertLog->execute([
    ':session_id' => $sessionId,
    ':media_id' => $mediaId,
    ':action' => $action,
    ':user_agent' => $userAgent,
    ':is_whatsapp' => $isWhatsApp
]);

// Serve the file
$mimeType = mime_content_type($filePath);
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
?>
