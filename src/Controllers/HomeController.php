<?php


namespace App\Controllers;

use App\Services\TestService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController
{
    private TestService $testService;

    public function __construct()
    {
        $this->testService = new TestService();
    }

    public function index(Request $request, Response $response): Response
    {
        $title = 'Dynamic Systems Lab';
        $message = $this->testService->getWelcomeMessage();

        ob_start();
        require __DIR__ . '/../../views/home.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }

    public function testJson(Request $request, Response $response): Response
    {
        $data = [
            'status' => 'ok',
            'message' => $this->testService->getWelcomeMessage(),
            'project' => 'dynamic-systems-lab'
        ];

        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}