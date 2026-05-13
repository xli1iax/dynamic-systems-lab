<?php

namespace App\Controllers;

use App\Models\Log;
use App\Services\LogService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogController
{
    private LogService $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function export(Request $request, Response $response): Response
    {
        $data = json_decode((string) $request->getBody(), true);

        $command = $data['command'] ?? '2+2';
        $result = $data['result'] ?? '4';
        $success = $data['success'] ?? true;
        $errorMessage = $data['error_message'] ?? null;
        $ipAddress = $request->getServerParams()['REMOTE_ADDR'] ?? null;

        $this->logService->saveCasRequest(
            Log::SOURCE_TEST,
            $command,
            $result,
            (bool) $success,
            $errorMessage,
            $ipAddress
        );

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'message' => 'Log saved'
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}