<?php

namespace App\Controllers;

use App\Services\AnimationService;
use App\Services\LogService;
use App\Services\AnimationUsageService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AnimationController
{
    public function __construct(
        private AnimationService $animationService,
        private LogService $logService,
        private AnimationUsageService $animationUsageService
    ) {}

    public function invertedPendulum(Request $request, Response $response): Response
    {
        return $this->handleAnimation(
            $request,
            $response,
            'inverted_pendulum',
            fn(array $data) => $this->animationService->invertedPendulum($data)
        );
    }

    public function ballBeam(Request $request, Response $response): Response
    {
        return $this->handleAnimation(
            $request,
            $response,
            'ball_beam',
            fn(array $data) => $this->animationService->ballBeam($data)
        );
    }

    private function handleAnimation(
        Request $request,
        Response $response,
        string $animationName,
        callable $callback
    ): Response {
        $data = json_decode($request->getBody()->getContents(), true) ?? [];

        $allowedKeys = [
            'r',
            'duration',
            'step',
            'initPosition',
            'initVelocity',
            'initAngle',
            'initAngularVelocity'
        ];

        $extraKeys = array_diff(array_keys($data), $allowedKeys);

        if (!empty($extraKeys)) {
            return $this->json($response, [
                'success' => false,
                'animation' => $animationName,
                'error' => 'Invalid animation parameters.',
                'extra_fields' => array_values($extraKeys),
            ], 400);
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        try {
            $result = $callback($data);

            $this->animationUsageService->track($animationName, $ip);

            $this->logService->saveCasRequest(
                'animation',
                $animationName . ': ' . json_encode($data),
                json_encode($result),
                true,
                null,
                $ip
            );

            return $this->json($response, [
                'success' => true,
                'animation' => $animationName,
                'data' => $result
            ]);

        } catch (\Throwable $e) {
            $this->logService->saveCasRequest(
                'animation',
                $animationName . ': ' . json_encode($data),
                null,
                false,
                $e->getMessage(),
                $ip
            );

            return $this->json($response, [
                'success' => false,
                'animation' => $animationName,
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