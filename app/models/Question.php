<?php
namespace App\Models;

use App\Core\Model;

class Question extends Model {
    protected string $table = 'questions';

    public function getWithUserDetails(int $userId = 0): array {
        $sql = "SELECT q.*, u.name AS user_name, u.email AS user_email, u.role AS user_role,
                       (SELECT COUNT(*) FROM replies r WHERE r.question_id = q.id) AS replies_count
                FROM questions q
                JOIN users u ON q.user_id = u.id";
        
        $params = [];
        if ($userId > 0) {
            $sql .= " WHERE q.user_id = :user_id";
            $params['user_id'] = $userId;
        }
        
        $sql .= " ORDER BY q.created_at DESC";
        return $this->query($sql, $params);
    }

    public function findWithUserDetails(int $id): ?array {
        $sql = "SELECT q.*, u.name AS user_name, u.email AS user_email, u.role AS user_role
                FROM questions q
                JOIN users u ON q.user_id = u.id
                WHERE q.id = :id LIMIT 1";
        return $this->queryRow($sql, ['id' => $id]);
    }
}
