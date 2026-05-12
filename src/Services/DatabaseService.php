<?php

namespace App\Services;

use PDO;

class DatabaseService
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getTestUsers(): array
    {
        $stmt = $this->connection->query("SELECT * FROM test_users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}