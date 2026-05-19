<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DocumentationController
{
    public function pdf(Request $request, Response $response): Response
    {
        $openApiPath = __DIR__ . '/../../public/openapi/openapi.json';

        $openApi = [];

        if (file_exists($openApiPath)) {
            $openApi = json_decode(file_get_contents($openApiPath), true) ?? [];
        }

        $title = 'API Documentation';
        $paths = $openApi['paths'] ?? [];

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: DejaVu Sans, sans-serif;
                    font-size: 12px;
                    color: #222;
                    margin: 90px 45px 70px 45px;
                }

                h1 {
                    font-size: 26px;
                    margin-bottom: 20px;
                }

                h2 {
                    font-size: 18px;
                    margin-top: 28px;
                    border-bottom: 1px solid #ccc;
                    padding-bottom: 6px;
                }

                .endpoint {
                    margin-bottom: 18px;
                    padding: 12px;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                }

                .method {
                    font-weight: bold;
                    text-transform: uppercase;
                    color: #1d4ed8;
                }

                .path {
                    font-family: DejaVu Sans Mono, monospace;
                }

                .header {
                    position: fixed;
                    top: -60px;
                    left: 0;
                    right: 0;
                    height: 40px;
                    text-align: center;
                    font-size: 14px;
                    font-weight: bold;
                    border-bottom: 1px solid #ccc;
                    padding-bottom: 8px;
                }

                .footer {
                    position: fixed;
                    bottom: -40px;
                    left: 0;
                    right: 0;
                    height: 30px;
                    text-align: center;
                    font-size: 11px;
                    color: #555;
                    border-top: 1px solid #ccc;
                    padding-top: 8px;
                }
            </style>
        </head>
        <body>

        <div class="header">API Documentation</div>
        <div class="footer">Page <span class="page-number"></span></div>

        <h1>API Documentation</h1>
        <p>This document is generated dynamically from the OpenAPI specification.</p>
        ';

        foreach ($paths as $path => $methods) {
            $html .= '<h2>' . htmlspecialchars($path) . '</h2>';

            foreach ($methods as $method => $details) {
                $summary = $details['summary'] ?? 'No summary';
                $description = $details['description'] ?? '';

                $html .= '
                <div class="endpoint">
                    <div><span class="method">' . htmlspecialchars($method) . '</span> 
                    <span class="path">' . htmlspecialchars($path) . '</span></div>
                    <p><strong>Summary:</strong> ' . htmlspecialchars($summary) . '</p>
                    <p>' . htmlspecialchars($description) . '</p>
                </div>';
            }
        }

        $html .= '
        </body>
        </html>';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');

        $canvas->page_text(
            270,
            820,
            '{PAGE_NUM}/{PAGE_COUNT}',
            $font,
            10,
            [0, 0, 0]
        );

        $pdf = $dompdf->output();

        $response->getBody()->write($pdf);

        return $response
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'inline; filename="api_documentation.pdf"');
    }
}