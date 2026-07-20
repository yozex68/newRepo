<?php
namespace App\Models;

use App\Core\Model;

class Subscription extends Model {
    protected string $table = 'subscriptions';

    public function getActiveSubscriptions(): array {
        $sql = "SELECT s.*, u.name AS user_name, u.email AS user_email, sp.name AS plan_name, sp.price AS plan_price
                FROM subscriptions s
                JOIN users u ON s.user_id = u.id
                JOIN subscription_plans sp ON s.subscription_plan_id = sp.id
                ORDER BY s.created_at DESC";
        return $this->query($sql);
    }

    public function findActiveByUser(int $userId): ?array {
        $sql = "SELECT s.*, sp.name AS plan_name, sp.max_downloads, sp.price AS plan_price 
                FROM subscriptions s
                JOIN subscription_plans sp ON s.subscription_plan_id = sp.id
                WHERE s.user_id = :user_id AND s.status = 'active' AND s.expires_at > CURRENT_TIMESTAMP
                LIMIT 1";
        return $this->queryRow($sql, ['user_id' => $userId]);
    }

    public function subscribe(int $userId, int $planId): int {
        // Fetch plan to get duration
        $planModel = new SubscriptionPlan();
        $plan = $planModel->find($planId);
        if (!$plan) {
            return 0;
        }

        // Deactivate previous active subscriptions for this user
        $sql = "UPDATE subscriptions SET status = 'cancelled' WHERE user_id = :user_id AND status = 'active'";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        $starts = date('Y-m-d H:i:s');
        $expires = date('Y-m-d H:i:s', strtotime("+{$plan['duration_months']} month"));

        // Update the user's main record's plan ID
        $userModel = new User();
        $userModel->update($userId, ['subscription_plan_id' => $planId]);

        return $this->create([
            'user_id' => $userId,
            'subscription_plan_id' => $planId,
            'status' => 'active',
            'starts_at' => $starts,
            'expires_at' => $expires
        ]);
    }
}
