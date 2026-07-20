<?php
namespace App\Models;

use App\Core\Model;

class Reply extends Model {
    protected string $table = 'replies';

    public function getRepliesByQuestion(int $questionId): array {
        $sql = "SELECT r.*, u.name AS user_name, u.role AS user_role, u.email AS user_email
                FROM replies r
                JOIN users u ON r.user_id = u.id
                WHERE r.question_id = :q_id
                ORDER BY r.created_at ASC";
        return $this->query($sql, ['q_id' => $questionId]);
    }
}
