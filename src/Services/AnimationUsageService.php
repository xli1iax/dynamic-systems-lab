<?php

namespace App\Services;

use PDO;

class AnimationUsageService
{
    public function __construct(private PDO $pdo) {}

    public function track(string $animationName, ?string $ip): void
    {
        $token = $this->getOrCreateUserToken();
        $interval = getAnimationStatsIntervalMinutes();

        if (!$this->canCountUsage($animationName, $token, $interval)) {
            return;
        }

        $location = $this->getLocationByIp($ip);

        $stmt = $this->pdo->prepare("
            INSERT INTO animation_usage 
                (animation_name, user_token, ip_address, city, country)
            VALUES
                (:animation_name, :user_token, :ip_address, :city, :country)
        ");

        $stmt->execute([
            'animation_name' => $animationName,
            'user_token' => $token,
            'ip_address' => $ip,
            'city' => $location['city'],
            'country' => $location['country'],
        ]);
    }

    private function getOrCreateUserToken(): string
    {
        if (!empty($_COOKIE['animation_user_token'])) {
            return $_COOKIE['animation_user_token'];
        }

        $token = bin2hex(random_bytes(32));

        setcookie('animation_user_token', $token, [
            'expires' => time() + 60 * 60 * 24 * 365,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        $_COOKIE['animation_user_token'] = $token;

        return $token;
    }

    private function canCountUsage(string $animationName, string $token, int $intervalMinutes): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT used_at
            FROM animation_usage
            WHERE animation_name = :animation_name
              AND user_token = :user_token
            ORDER BY used_at DESC
            LIMIT 1
        ");

        $stmt->execute([
            'animation_name' => $animationName,
            'user_token' => $token,
        ]);

        $lastUsedAt = $stmt->fetchColumn();

        if (!$lastUsedAt) {
            return true;
        }

        return strtotime($lastUsedAt) <= time() - ($intervalMinutes * 60);
    }

    private function getLocationByIp(?string $ip): array
    {
        if (!$ip || $ip === '127.0.0.1' || $ip === '::1') {
            return [
                'city' => 'Localhost',
                'country' => 'Localhost',
            ];
        }

        $json = @file_get_contents("http://ip-api.com/json/" . urlencode($ip) . "?fields=status,country,city");

        if (!$json) {
            return [
                'city' => null,
                'country' => null,
            ];
        }

        $data = json_decode($json, true);

        if (($data['status'] ?? '') !== 'success') {
            return [
                'city' => null,
                'country' => null,
            ];
        }

        return [
            'city' => $data['city'] ?? null,
            'country' => $data['country'] ?? null,
        ];
    }
}