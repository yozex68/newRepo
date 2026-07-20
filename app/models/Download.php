<?php
namespace App\Models;

use App\Core\Model;

class Download extends Model {
    protected string $table = 'downloads';

    public function log(int $userId, int $materialId, string $ipAddress, string $userAgent): int {
        return $this->create([
            'user_id' => $userId,
            'material_id' => $materialId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);
    }

    public function getMostDownloaded(int $limit = 5): array {
        $sql = "SELECT m.title, m.material_type, c.code AS course_code, COUNT(d.id) AS download_count 
                FROM downloads d
                JOIN materials m ON d.material_id = m.id
                JOIN courses c ON m.course_id = c.id
                GROUP BY d.material_id
                ORDER BY download_count DESC LIMIT :limit";
        
        $stmt = $this->getDb()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDownloadTrends(int $days = 7): array {
        $sql = "SELECT DATE(downloaded_at) AS date_label, COUNT(*) AS count 
                FROM downloads 
                WHERE downloaded_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY DATE(downloaded_at)
                ORDER BY date_label ASC";
        
        $stmt = $this->getDb()->prepare($sql);
        $stmt->bindValue(':days', $days, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDetailedReport(): array {
        $sql = "SELECT d.downloaded_at, d.ip_address, u.name AS user_name, u.email AS user_email, 
                       m.title AS file_title, m.material_type, c.code AS course_code
                FROM downloads d
                JOIN users u ON d.user_id = u.id
                JOIN materials m ON d.material_id = m.id
                JOIN courses c ON m.course_id = c.id
                ORDER BY d.downloaded_at DESC";
        return $this->query($sql);
    }
}
