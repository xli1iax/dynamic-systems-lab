<?php

namespace App\Controllers;

use App\Services\AnimationStatisticsService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AnimationStatisticsController
{
    public function __construct(
        private AnimationStatisticsService $statisticsService
    ) {}

    public function summary(Request $request, Response $response): Response
    {
        return $this->json($response, [
            'success' => true,
            'data' => $this->statisticsService->getSummary()
        ]);
    }

    public function details(Request $request, Response $response, array $args): Response
    {
        $animationName = $args['name'] ?? '';

        return $this->json($response, [
            'success' => true,
            'animation' => $animationName,
            'data' => $this->statisticsService->getDetails($animationName)
        ]);
    }

    private function json(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}