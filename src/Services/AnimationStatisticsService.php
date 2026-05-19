<?php

namespace App\Services;

use PDO;

class AnimationStatisticsService
{
    public function __construct(private PDO $pdo) {}

    public function getSummary(): array
    {
        $stmt = $this->pdo->query("
            SELECT animation_name, COUNT(*) AS total_uses
            FROM animation_usage
            GROUP BY animation_name
            ORDER BY animation_name
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetails(string $animationName): array
    {
        $stmt = $this->pdo->prepare("
            SELECT animation_name, user_token, city, country, used_at
            FROM animation_usage
            WHERE animation_name = :animation_name
            ORDER BY used_at DESC
        ");

        $stmt->execute([
            'animation_name' => $animationName
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}