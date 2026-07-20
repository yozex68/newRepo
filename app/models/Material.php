<?php
namespace App\Models;

use App\Core\Model;

class Material extends Model {
    protected string $table = 'materials';

    public function search(array $filters): array {
        $sql = "SELECT m.*, c.name AS course_name, c.code AS course_code, c.lecturer AS course_lecturer, 
                       p.name AS programme_name, y.name AS year_name, s.name AS semester_name, u.name AS uploader_name
                FROM materials m
                JOIN courses c ON m.course_id = c.id
                JOIN programmes p ON c.programme_id = p.id
                JOIN years y ON c.year_id = y.id
                JOIN semesters s ON c.semester_id = s.id
                JOIN users u ON m.uploader_id = u.id
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['q'])) {
            $sql .= " AND (m.title LIKE :q_title OR m.description LIKE :q_desc)";
            $params['q_title'] = "%" . $filters['q'] . "%";
            $params['q_desc'] = "%" . $filters['q'] . "%";
        }

        if (!empty($filters['course_name'])) {
            $sql .= " AND c.name LIKE :course_name";
            $params['course_name'] = "%" . $filters['course_name'] . "%";
        }

        if (!empty($filters['course_code'])) {
            $sql .= " AND c.code = :course_code";
            $params['course_code'] = $filters['course_code'];
        }

        if (!empty($filters['lecturer'])) {
            $sql .= " AND c.lecturer LIKE :lecturer";
            $params['lecturer'] = "%" . $filters['lecturer'] . "%";
        }

        if (!empty($filters['programme_id'])) {
            $sql .= " AND c.programme_id = :programme_id";
            $params['programme_id'] = (int)$filters['programme_id'];
        }

        if (!empty($filters['year_id'])) {
            $sql .= " AND c.year_id = :year_id";
            $params['year_id'] = (int)$filters['year_id'];
        }

        if (!empty($filters['semester_id'])) {
            $sql .= " AND c.semester_id = :semester_id";
            $params['semester_id'] = (int)$filters['semester_id'];
        }

        if (!empty($filters['material_type'])) {
            $sql .= " AND m.material_type = :material_type";
            $params['material_type'] = $filters['material_type'];
        }

        // Apply access restrictions if user is not Admin
        if (isset($filters['allowed_programme_ids']) && is_array($filters['allowed_programme_ids'])) {
            if (empty($filters['allowed_programme_ids'])) {
                $sql .= " AND 1=0"; // block access to everything if no programs allowed
            } else {
                $placeholders = implode(',', array_map(fn($idx) => ":ap_" . $idx, array_keys($filters['allowed_programme_ids'])));
                $sql .= " AND c.programme_id IN ({$placeholders})";
                foreach ($filters['allowed_programme_ids'] as $idx => $id) {
                    $params['ap_' . $idx] = $id;
                }
            }
        }

        $sql .= " ORDER BY m.id DESC";
        return $this->query($sql, $params);
    }

    public function findWithDetails(int $id): ?array {
        $sql = "SELECT m.*, c.name AS course_name, c.code AS course_code, c.lecturer AS course_lecturer,
                       c.programme_id, p.name AS programme_name, y.name AS year_name, s.name AS semester_name
                FROM materials m
                JOIN courses c ON m.course_id = c.id
                JOIN programmes p ON c.programme_id = p.id
                JOIN years y ON c.year_id = y.id
                JOIN semesters s ON c.semester_id = s.id
                WHERE m.id = :id LIMIT 1";
        return $this->queryRow($sql, ['id' => $id]);
    }

    public function incrementDownloads(int $id): void {
        $sql = "UPDATE materials SET downloads_count = downloads_count + 1 WHERE id = :id";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public function getRecentUploads(int $limit = 5): array {
        $sql = "SELECT m.*, c.name AS course_name, c.code AS course_code, u.name AS uploader_name
                FROM materials m
                JOIN courses c ON m.course_id = c.id
                JOIN users u ON m.uploader_id = u.id
                ORDER BY m.created_at DESC LIMIT :limit";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countByType(): array {
        $sql = "SELECT material_type, COUNT(*) AS count FROM materials GROUP BY material_type";
        return $this->query($sql);
    }

    public function checkDuplicateHash(string $hash): ?array {
        return $this->findBy('file_hash', $hash);
    }
}
