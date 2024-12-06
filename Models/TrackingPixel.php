<?php
// File: app/Models/TrackingPixel.php
namespace html\Models;
use PDO;

class TrackingPixel
{
    public $id;
    public $page_id;
    public $name;
    public $image_path;
    public $download_speed;
    public $created_at;
    public $updated_at;

    /**
     * Fetch all tracking pixels for a page
     */
    public static function findByPageId($pageId)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM tracking_pixel_configs WHERE page_id = :page_id");
        $stmt->execute([':page_id' => $pageId]);
        $pixels = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pixel = new TrackingPixel();
            foreach ($data as $key => $value) {
                $pixel->$key = $value;
            }
            $pixels[] = $pixel;
        }
        return $pixels;
    }

    /**
     * Find tracking pixel by ID
     */
    public static function find($id)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM tracking_pixel_configs WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $pixel = new TrackingPixel();
            foreach ($data as $key => $value) {
                $pixel->$key = $value;
            }
            return $pixel;
        }
        return null;
    }

    /**
     * Save (insert or update) tracking pixel
     */
    public function save()
    {
        $pdo = Database::getInstance();
        if (isset($this->id)) {
            // Update existing tracking pixel
            $stmt = $pdo->prepare("UPDATE tracking_pixel_configs SET name = :name, image_path = :image_path, download_speed = :download_speed, updated_at = NOW() WHERE id = :id");
            $stmt->execute([
                ':name' => $this->name,
                ':image_path' => $this->image_path,
                ':download_speed' => $this->download_speed,
                ':id' => $this->id
            ]);
        } else {
            // Insert new tracking pixel
            $stmt = $pdo->prepare("INSERT INTO tracking_pixel_configs (page_id, name, image_path, download_speed, created_at, updated_at) VALUES (:page_id, :name, :image_path, :download_speed, NOW(), NOW())");
            $stmt->execute([
                ':page_id' => $this->page_id,
                ':name' => $this->name,
                ':image_path' => $this->image_path,
                ':download_speed' => $this->download_speed
            ]);
            $this->id = $pdo->lastInsertId();
        }
    }

    /**
     * Delete tracking pixel
     */
    public function delete()
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("DELETE FROM tracking_pixel_configs WHERE id = :id");
        $stmt->execute([':id' => $this->id]);
    }
}
