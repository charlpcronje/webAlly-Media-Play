<?php
// File: app/Models/MediaLog.php
namespace html\Models;
use \PDO;

class MediaLog
{
    public $id;
    public $session_id;
    public $media_id;
    public $action;
    public $timestamp;
    public $current_time;
    public $user_agent;
    public $is_whatsapp;

    /**
     * Save media log
     */
    public function save()
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO media_logs (session_id, media_id, action, timestamp, current_time, user_agent, is_whatsapp) VALUES (:session_id, :media_id, :action, NOW(), :current_time, :user_agent, :is_whatsapp)");
        $stmt->execute([
            ':session_id' => $this->session_id,
            ':media_id' => $this->media_id,
            ':action' => $this->action,
            ':current_time' => $this->current_time,
            ':user_agent' => $this->user_agent,
            ':is_whatsapp' => $this->is_whatsapp
        ]);
    }
}
