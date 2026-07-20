<?php
namespace App\Models;

use App\Core\Model;
use App\Helpers\Encryption;

class User extends Model {
    protected string $table = 'users';

    public function create(array $data): int {
        if (isset($data['phone'])) {
            $data['encrypted_phone'] = Encryption::encrypt($data['phone']);
            unset($data['phone']);
        }
        return parent::create($data);
    }

    public function update(int $id, array $data): bool {
        if (isset($data['phone'])) {
            $data['encrypted_phone'] = Encryption::encrypt($data['phone']);
            unset($data['phone']);
        }
        return parent::update($id, $data);
    }

    public function findWithDetails(int $id): ?array {
        $sql = "SELECT u.*, p.name AS programme_name, p.code AS programme_code, f.name AS faculty_name, s.name AS plan_name 
                FROM users u
                LEFT JOIN programmes p ON u.programme_id = p.id
                LEFT JOIN faculties f ON p.faculty_id = f.id
                LEFT JOIN subscription_plans s ON u.subscription_plan_id = s.id
                WHERE u.id = :id LIMIT 1";
        return $this->queryRow($sql, ['id' => $id]);
    }

    public function getPhone(array $user): string {
        return !empty($user['encrypted_phone']) ? Encryption::decrypt($user['encrypted_phone']) : '';
    }

    public function findByEmail(string $email): ?array {
        return $this->findBy('email', $email);
    }

    /**
     * Check if a student is enrolled in a course's programme, or has admin override permission.
     */
    public function hasAccessToProgramme(int $userId, int $programmeId): bool {
        $user = $this->find($userId);
        if (!$user) return false;
        
        // Admins have access to everything
        if ($user['role'] === 'admin') {
            return true;
        }

        // Standard student checking
        if ($user['role'] === 'student' && (int)$user['programme_id'] === $programmeId) {
            return true;
        }

        // Check explicit permissions
        $sql = "SELECT id FROM student_permissions WHERE user_id = :user_id AND programme_id = :prog_id LIMIT 1";
        $perm = $this->queryRow($sql, ['user_id' => $userId, 'prog_id' => $programmeId]);
        
        return $perm !== null;
    }

    /**
     * Check if the user has an active premium subscription.
     */
    public function isPremium(int $userId): bool {
        $sql = "SELECT s.* 
                FROM subscriptions s
                JOIN subscription_plans sp ON s.subscription_plan_id = sp.id
                WHERE s.user_id = :user_id AND s.status = 'active' AND s.expires_at > CURRENT_TIMESTAMP 
                LIMIT 1";
        return $this->queryRow($sql, ['user_id' => $userId]) !== null;
    }
}
