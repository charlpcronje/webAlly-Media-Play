<?php
// File: app/Models/PageView.php
namespace html\Models;
use \PDO;

class PageView
{
    public $id;
    public $page_id;
    public $session_id;
    public $start_time;
    public $end_time;
    public $duration_seconds;

    /**
     * Fetch all page views with page names
     */
    public static function allWithPages()
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("
            SELECT pv.id, p.name AS page_name, pv.session_id, pv.start_time, pv.end_time, TIMESTAMPDIFF(SECOND, pv.start_time, pv.end_time) AS duration_seconds
            FROM page_views pv
            JOIN pages p ON pv.page_id = p.id
            ORDER BY pv.start_time DESC
        ");

        $pageViews = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pageViews[] = $data;
        }

        return $pageViews;
    }

    /**
     * Save page view
     */
    public function save()
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO page_views (page_id, session_id, start_time, end_time, duration_seconds) VALUES (:page_id, :session_id, :start_time, :end_time, :duration_seconds)");
        $stmt->execute([
            ':page_id' => $this->page_id,
            ':session_id' => $this->session_id,
            ':start_time' => $this->start_time,
            ':end_time' => $this->end_time,
            ':duration_seconds' => $this->duration_seconds
        ]);
    }
}
