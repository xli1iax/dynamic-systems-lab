<?php

namespace App\Models;

use PDO;

class Log
{
    public const SOURCE_TEST = 'test';
    private PDO $db;


    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(
        string $source,
        string $command,
        ?string $result,
        bool $success,
        ?string $errorMessage,
        ?string $ipAddress
    ): void {
        $stmt = $this->db->prepare("
            INSERT INTO logs (
                source,
                command,
                result,
                success,
                error_message,
                ip_address
            )
            VALUES (
                :source,
                :command,
                :result,
                :success,
                :error_message,
                :ip_address
            )
        ");

        $stmt->execute([
            ':source' => $source,
            ':command' => $command,
            ':result' => $result,
            ':success' => $success ? 1 : 0,
            ':error_message' => $errorMessage,
            ':ip_address' => $ipAddress,
        ]);
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT *
            FROM logs
            ORDER BY created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}