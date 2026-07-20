<?php
namespace App\Models;

use App\Core\Model;

class AuditLog extends Model {
    protected string $table = 'audit_logs';

    public function log(?int $userId, string $action, string $details = ''): int {
        return $this->create([
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'details' => $details
        ]);
    }

    public function getWithUserDetails(int $limit = 50): array {
        $sql = "SELECT a.*, u.name AS user_name, u.email AS user_email, u.role AS user_role
                FROM audit_logs a
                LEFT JOIN users u ON a.user_id = u.id
                ORDER BY a.created_at DESC LIMIT :limit";
        
        $stmt = $this->getDb()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
