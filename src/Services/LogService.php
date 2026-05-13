<?php

namespace App\Services;

use App\Models\Log;

class LogService
{
    private Log $logModel;

    public function __construct(Log $logModel)
    {
        $this->logModel = $logModel;
    }

    public function saveCasRequest(
        string $source,
        string $command,
        ?string $result,
        bool $success,
        ?string $errorMessage,
        ?string $ipAddress
    ): void {
        $this->logModel->create(
            $source,
            $command,
            $result,
            $success,
            $errorMessage,
            $ipAddress
        );
    }

    public function getLogs(): array
    {
        return $this->logModel->findAll();
    }
}