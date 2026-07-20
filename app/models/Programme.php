<?php
namespace App\Models;

use App\Core\Model;

class Programme extends Model {
    protected string $table = 'programmes';

    public function getWithFacultyDetails(): array {
        $sql = "SELECT p.*, f.name AS faculty_name 
                FROM programmes p
                JOIN faculties f ON p.faculty_id = f.id
                ORDER BY p.name ASC";
        return $this->query($sql);
    }

    public function getByFaculty(int $facultyId): array {
        $sql = "SELECT * FROM programmes WHERE faculty_id = :fac_id ORDER BY name ASC";
        return $this->query($sql, ['fac_id' => $facultyId]);
    }
}
