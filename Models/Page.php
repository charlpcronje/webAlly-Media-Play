<?php
// File: app/Models/Page.php
namespace html\Models;
use \PDO;

class Page
{
    public $id;
    public $name;
    public $slug;
    public $is_archived;
    public $created_at;
    public $updated_at;

    /**
     * Fetch all pages, optionally including archived
     */
    public static function all($includeArchived = false)
    {
        $pdo = Database::getInstance();
        if ($includeArchived) {
            $stmt = $pdo->query("SELECT * FROM pages ORDER BY created_at DESC");
        } else {
            $stmt = $pdo->prepare("SELECT * FROM pages WHERE is_archived = 0 ORDER BY created_at DESC");
            $stmt->execute();
        }

        $pages = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $page = new Page();
            foreach ($data as $key => $value) {
                $page->$key = $value;
            }
            $pages[] = $page;
        }
        return $pages;
    }

    /**
     * Find a page by ID
     */
    public static function find($id)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $page = new Page();
            foreach ($data as $key => $value) {
                $page->$key = $value;
            }
            return $page;
        }
        return null;
    }

    /**
     * Save (insert or update) the page
     */
    public function save()
    {
        $pdo = Database::getInstance();
        if (isset($this->id)) {
            // Update existing page
            $stmt = $pdo->prepare("UPDATE pages SET name = :name, slug = :slug, is_archived = :is_archived, updated_at = NOW() WHERE id = :id");
            $stmt->execute([
                ':name' => $this->name,
                ':slug' => $this->slug,
                ':is_archived' => $this->is_archived,
                ':id' => $this->id
            ]);
        } else {
            // Insert new page
            $stmt = $pdo->prepare("INSERT INTO pages (name, slug, is_archived, created_at, updated_at) VALUES (:name, :slug, :is_archived, NOW(), NOW())");
            $stmt->execute([
                ':name' => $this->name,
                ':slug' => $this->slug,
                ':is_archived' => $this->is_archived
            ]);
            $this->id = $pdo->lastInsertId();
        }
    }

    /**
     * Fetch page views with associated page names
     */
    public static function allWithPageViews()
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
}
