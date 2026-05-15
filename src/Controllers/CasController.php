<?php

namespace App\Controllers;

use App\Services\OctaveService;
use App\Services\LogService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CasController
{
    private OctaveService $octaveService;
    private LogService $logService;

    public function __construct(
        OctaveService $octaveService,
        LogService $logService
    ) {
        $this->logService = $logService;
        $this->octaveService = $octaveService;
    }

    public function execute(Request $request, Response $response): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = json_decode($request->getBody()->getContents(), true);

        $command = trim($data['command'] ?? '');
        $source = $data['source'] ?? 'api';
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        $_SESSION['cas_history'] ??= [];

        if ($command === '') {
            return $this->json($response, [
                'success' => false,
                'error' => 'Command is required'
            ], 400);
        }

        $forbidden = [
            'system', 'unix', 'dos', 'delete', 'rmdir', 'mkdir',
            'fopen', 'fclose', 'fprintf', 'save', 'load',
            'cd', 'pwd', 'ls', 'dir', 'exit', 'quit'
        ];

        foreach ($forbidden as $word) {
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $command)) {
                return $this->json($response, [
                    'success' => false,
                    'error' => 'Forbidden command'
                ], 400);
            }
        }

        if (strlen($command) > 1000) {
            return $this->json($response, [
                'success' => false,
                'error' => 'Command is too long'
            ], 400);
        }

        $historyScript = '';

        if (!empty($_SESSION['cas_history'])) {
            $historyScript = implode(";" . PHP_EOL, $_SESSION['cas_history']) . ";" . PHP_EOL;
        }

        if (preg_match('/^\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*=/', $command, $matches)) {
            $variableName = $matches[1];

            $fullCommand = $historyScript
                . $command . ";" . PHP_EOL
                . "__cas_result__ = " . $variableName . ";";
        } else {
            $fullCommand = $historyScript
                . "__cas_result__ = (" . $command . ");";
        }

        try {
            $result = $this->octaveService->execute($fullCommand);

            if (preg_match('/^\s*[a-zA-Z_][a-zA-Z0-9_]*\s*=/', $command)) {
                $_SESSION['cas_history'][] = $command;
            }

            $this->logService->saveCasRequest(
                $source,
                $command,
                json_encode($result),
                true,
                null,
                $ip
            );

            return $this->json($response, [
                'success' => true,
                'result' => $result
            ]);

        } catch (\Throwable $e) {
            $this->logService->saveCasRequest(
                $source,
                $command,
                null,
                false,
                $e->getMessage(),
                $ip
            );

            return $this->json($response, [
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function json(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}