<?php
namespace App\Models;

use App\Core\Model;

class Announcement extends Model {
    protected string $table = 'announcements';

    public function getWithCreatorDetails(int $limit = 10): array {
        $sql = "SELECT a.*, u.name AS creator_name 
                FROM announcements a
                JOIN users u ON a.creator_id = u.id
                ORDER BY a.created_at DESC LIMIT :limit";
        
        $stmt = $this->getDb()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
