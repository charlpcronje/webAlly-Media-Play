<?php
// File: app/Models/TrackingPixelSession.php

namespace html\Models;
use \PDO;

class TrackingPixelSession
{
    public $id;
    public $tracking_id;
    public $tracking_pixel_id;
    public $page_id;
    public $created_at;
    public $updated_at;

    /**
     * Find session by tracking_id
     */
    public static function findByTrackingId($trackingId)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM tracking_pixel_sessions WHERE tracking_id = :tracking_id LIMIT 1");
        $stmt->execute([':tracking_id' => $trackingId]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            $session = new TrackingPixelSession();
            foreach ($data as $key => $value) {
                $session->$key = $value;
            }
            return $session;
        }
        return null;
    }

    /**
     * Create a new tracking pixel session
     */
    public static function create($pageId, $trackingId, $trackingPixelId)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO tracking_pixel_sessions (tracking_id, tracking_pixel_id, page_id, created_at, updated_at) VALUES (:tracking_id, :tracking_pixel_id, :page_id, NOW(), NOW())");
        $stmt->execute([
            ':tracking_id' => $trackingId,
            ':tracking_pixel_id' => $trackingPixelId,
            ':page_id' => $pageId
        ]);
        return self::findByTrackingId($trackingId);
    }
}
