<?php
// File: app/Models/Media.php
namespace html\Models;
use \PDO;

class Media
{
    public $id;
    public $page_id;
    public $file_path;
    public $media_type;
    public $props;
    public $created_at;
    public $updated_at;

    /**
     * Fetch all media
     */
    public static function all()
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM media ORDER BY created_at DESC");

        $media = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $m = new Media();
            foreach ($data as $key => $value) {
                $m->$key = $value;
            }
            $media[] = $m;
        }
        return $media;
    }

    /**
     * Find a media by ID
     */
    public static function find($id)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM media WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $media = new Media();
            foreach ($data as $key => $value) {
                $media->$key = $value;
            }
            return $media;
        }
        return null;
    }

    /**
     * Find media by file path
     */
    public static function findByFilePath($fileName)
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM media WHERE file_path LIKE :filePath LIMIT 1");
        $stmt->execute([':filePath' => "%$fileName"]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $media = new Media();
            foreach ($data as $key => $value) {
                $media->$key = $value;
            }
            return $media;
        }
        return null;
    }

    /**
     * Save (insert or update) media
     */
    public function save()
    {
        $pdo = Database::getInstance();
        if (isset($this->id)) {
            // Update existing media
            $stmt = $pdo->prepare("UPDATE media SET page_id = :page_id, file_path = :file_path, media_type = :media_type, props = :props, updated_at = NOW() WHERE id = :id");
            $stmt->execute([
                ':page_id' => $this->page_id,
                ':file_path' => $this->file_path,
                ':media_type' => $this->media_type,
                ':props' => $this->props,
                ':id' => $this->id
            ]);
        } else {
            // Insert new media
            $stmt = $pdo->prepare("INSERT INTO media (page_id, file_path, media_type, props, created_at, updated_at) VALUES (:page_id, :file_path, :media_type, :props, NOW(), NOW())");
            $stmt->execute([
                ':page_id' => $this->page_id,
                ':file_path' => $this->file_path,
                ':media_type' => $this->media_type,
                ':props' => $this->props
            ]);
            $this->id = $pdo->lastInsertId();
        }
    }
}
