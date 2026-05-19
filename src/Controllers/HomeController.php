<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController
{
    public function index(Request $request, Response $response): Response
    {
        ob_start();
        require __DIR__ . '/../../views/home.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }
}