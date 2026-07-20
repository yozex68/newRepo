<?php
namespace App\Models;

use App\Core\Model;

class Faculty extends Model {
    protected string $table = 'faculties';

    public function getWithProgrammesCount(): array {
        $sql = "SELECT f.*, COUNT(p.id) AS programmes_count 
                FROM faculties f
                LEFT JOIN programmes p ON f.id = p.faculty_id
                GROUP BY f.id
                ORDER BY f.name ASC";
        return $this->query($sql);
    }
}
