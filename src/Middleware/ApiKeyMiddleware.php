<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ApiKeyMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $apiKey = $request->getHeaderLine('X-API-KEY');

        if ($apiKey !== getApiKey()) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'error' => 'Unauthorized. Invalid API key.'
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        return $handler->handle($request);
    }
}