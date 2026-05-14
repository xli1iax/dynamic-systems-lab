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
        $data = json_decode($request->getBody()->getContents(), true);

        $command = $data['command'] ?? '';
        $source = $data['source'] ?? 'api';
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        if (trim($command) === '') {
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

        try {
            $result = $this->octaveService->execute($command);

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

    public function executeScript(string $script): mixed
    {
        $delayMs = getCasDelayMs();

        if ($delayMs > 0) {
            usleep($delayMs * 1000);
        }

        $process = new Process([
            'octave',
            '--quiet',
            '--eval',
            $script
        ]);

        $process->setTimeout(20);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(trim($process->getErrorOutput()));
        }

        $output = trim($process->getOutput());

        if ($output === '') {
            throw new \RuntimeException('Empty output from Octave.');
        }

        $jsonStart = strpos($output, '{');
        $jsonEnd = strrpos($output, '}');

        if ($jsonStart === false || $jsonEnd === false || $jsonEnd <= $jsonStart) {
            throw new \RuntimeException('JSON output not found in Octave response: ' . $output);
        }

        $json = substr($output, $jsonStart, $jsonEnd - $jsonStart + 1);

        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON output from Octave: ' . $json);
        }

        return $decoded;
    }

    private function json(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}