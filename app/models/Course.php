<?php
namespace App\Models;

use App\Core\Model;

class Course extends Model {
    protected string $table = 'courses';

    public function getWithDetails(): array {
        $sql = "SELECT c.*, p.name AS programme_name, p.code AS programme_code, y.name AS year_name, s.name AS semester_name 
                FROM courses c
                JOIN programmes p ON c.programme_id = p.id
                JOIN years y ON c.year_id = y.id
                JOIN semesters s ON c.semester_id = s.id
                ORDER BY c.code ASC";
        return $this->query($sql);
    }

    public function findWithDetails(int $id): ?array {
        $sql = "SELECT c.*, p.name AS programme_name, p.code AS programme_code, y.name AS year_name, s.name AS semester_name 
                FROM courses c
                JOIN programmes p ON c.programme_id = p.id
                JOIN years y ON c.year_id = y.id
                JOIN semesters s ON c.semester_id = s.id
                WHERE c.id = :id LIMIT 1";
        return $this->queryRow($sql, ['id' => $id]);
    }

    public function getByCurriculum(int $programmeId, int $yearId, int $semesterId): array {
        $sql = "SELECT * FROM courses 
                WHERE programme_id = :prog_id AND year_id = :year_id AND semester_id = :sem_id 
                ORDER BY name ASC";
        return $this->query($sql, [
            'prog_id' => $programmeId,
            'year_id' => $yearId,
            'sem_id' => $semesterId
        ]);
    }
}
