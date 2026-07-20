<?php
namespace App\Models;

use App\Core\Model;

class Bookmark extends Model {
    protected string $table = 'bookmarks';

    public function getUserBookmarks(int $userId): array {
        $sql = "SELECT b.id AS bookmark_id, b.created_at AS bookmarked_at, m.*, c.name AS course_name, c.code AS course_code 
                FROM bookmarks b
                JOIN materials m ON b.material_id = m.id
                JOIN courses c ON m.course_id = c.id
                WHERE b.user_id = :user_id
                ORDER BY b.created_at DESC";
        return $this->query($sql, ['user_id' => $userId]);
    }

    public function isBookmarked(int $userId, int $materialId): bool {
        $sql = "SELECT id FROM bookmarks WHERE user_id = :user_id AND material_id = :mat_id LIMIT 1";
        return $this->queryRow($sql, ['user_id' => $userId, 'mat_id' => $materialId]) !== null;
    }

    public function removeBookmark(int $userId, int $materialId): bool {
        $sql = "DELETE FROM bookmarks WHERE user_id = :user_id AND material_id = :mat_id";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute(['user_id' => $userId, 'mat_id' => $materialId]);
    }
}
