<?php

use App\Controllers\AnimationController;
use App\Controllers\CasController;
use App\Controllers\HomeController;
use App\Controllers\LogController;
use App\Middleware\ApiKeyMiddleware;
use App\Models\Log;
use App\Services\AnimationService;
use App\Services\DatabaseService;
use App\Services\LogService;
use App\Services\OctaveService;
use Slim\Factory\AppFactory;
use App\Services\AnimationUsageService;

use App\Controllers\AnimationStatisticsController;
use App\Services\AnimationStatisticsService;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$app = AppFactory::create();

$pdo = connectDatabase();

$databaseService = new DatabaseService($pdo);

$logModel = new Log($pdo);
$logService = new LogService($logModel);

$homeController = new HomeController($databaseService);
$logController = new LogController($logService);

$octaveService = new OctaveService();
$casController = new CasController($octaveService, $logService);


$animationService = new AnimationService($octaveService);
$animationUsageService = new AnimationUsageService($pdo);
$animationController = new AnimationController(
    $animationService,
    $logService,
    $animationUsageService
);

$animationStatisticsService = new AnimationStatisticsService($pdo);
$animationStatisticsController = new AnimationStatisticsController($animationStatisticsService);
$app->get('/cas', function ($request, $response) {
    ob_start();
    require __DIR__ . '/../views/cas.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response;
});

$app->get('/animations/pendulum', function ($request, $response) {
    ob_start();
    require __DIR__ . '/../views/inverted-pendulum.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response;
});

$app->get('/animations/ball-beam', function ($request, $response) {
    ob_start();
    require __DIR__ . '/../views/ball-beam.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response;
});

$app->get('/statistics', function ($request, $response) {
    ob_start();
    require __DIR__ . '/../views/statistics.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response;
});

$app->get('/logs', function ($request, $response) {
    ob_start();
    require __DIR__ . '/../views/logs.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response;
});



$app->post('/api/animations/pendulum', [$animationController, 'invertedPendulum'])
    ->add(new ApiKeyMiddleware());

$app->post('/api/animations/ball-beam', [$animationController, 'ballBeam'])
    ->add(new ApiKeyMiddleware());

$app->post('/api/cas/execute', [$casController, 'execute'])
    ->add(new ApiKeyMiddleware());

$app->get('/docs', function ($request, $response) {
    ob_start();
    require __DIR__ . '/../views/api-docs.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response;
});
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->get('/', [$homeController, 'index']);
$app->get('/api/logs', [$logController, 'index'])
    ->add(new ApiKeyMiddleware());

$app->get('/api/logs/export', [$logController, 'export'])
    ->add(new ApiKeyMiddleware());

$app->get('/api/statistics/animations', [$animationStatisticsController, 'summary'])
    ->add(new ApiKeyMiddleware());

$app->get('/api/statistics/animations/{name}', [$animationStatisticsController, 'details'])
    ->add(new ApiKeyMiddleware());


$app->run();
