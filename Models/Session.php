<?php
// File: app/Models/Session.php
namespace html\Models;
use \PDO;

class Session
{
    public $id;
    public $ip_address;
    public $start_time;
    public $end_time;
    public $is_filtered;
    public $created_at;
    public $updated_at;

    /**
     * Get or create a session based on IP address
     */
    public static function getOrCreateSession($ipAddress)
    {
        $pdo = Database::getInstance();

        // Check if a current session exists (last end_time)
        $stmt = $pdo->prepare("SELECT * FROM sessions WHERE ip_address = :ip_address ORDER BY id DESC LIMIT 1");
        $stmt->execute([':ip_address' => $ipAddress]);
        $sessionData = $stmt->fetch(PDO::FETCH_ASSOC);

        $now = time();

        if ($sessionData) {
            $endTime = strtotime($sessionData['end_time']);
            if (($now - $endTime) < (30 * 60)) { // 30 minutes
                // Update end_time to now
                $update = $pdo->prepare("UPDATE sessions SET end_time = NOW() WHERE id = :id");
                $update->execute([':id' => $sessionData['id']]);
                $session = new Session();
                foreach ($sessionData as $key => $value) {
                    $session->$key = $value;
                }
                return $session;
            }
        }

        // Create new session
        $stmt = $pdo->prepare("INSERT INTO sessions (ip_address, start_time, end_time, created_at, updated_at) VALUES (:ip, NOW(), NOW(), NOW(), NOW())");
        $stmt->execute([':ip' => $ipAddress]);
        $sessionId = $pdo->lastInsertId();
        return self::find($sessionId);
    }

    /**
     * Find session by ID
     */
    public static function find($id)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM sessions WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $session = new Session();
            foreach ($data as $key => $value) {
                $session->$key = $value;
            }
            return $session;
        }
        return null;
    }
}
