<?php

namespace App\Controllers;

use App\Services\LogService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogController
{
    public function __construct(
        private LogService $logService
    ) {}

    public function index(Request $request, Response $response): Response
    {
        return $this->json($response, [
            'success' => true,
            'data' => $this->logService->getLogs()
        ]);
    }

    public function export(Request $request, Response $response): Response
    {
        $logs = $this->logService->getLogs();

        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, [
            'id',
            'source',
            'command',
            'result',
            'success',
            'error_message',
            'ip_address',
            'created_at'
        ], ',', '"', '\\');

        foreach ($logs as $log) {
            fputcsv($handle, [
                $log['id'] ?? '',
                $log['source'] ?? '',
                $log['command'] ?? '',
                $log['result'] ?? '',
                $log['success'] ?? '',
                $log['error_message'] ?? '',
                $log['ip_address'] ?? '',
                $log['created_at'] ?? '',
            ], ',', '"', '\\');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $response->getBody()->write($csv);

        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="cas_logs.csv"');
    }

    private function json(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}