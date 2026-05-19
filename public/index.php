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

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config.php';

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
$animationController = new AnimationController($animationService, $logService);

$app->post('/api/animations/pendulum', [$animationController, 'invertedPendulum'])
    ->add(new ApiKeyMiddleware());

$app->post('/api/animations/ball-beam', [$animationController, 'ballBeam'])
    ->add(new ApiKeyMiddleware());

$app->post('/api/cas/execute', [$casController, 'execute'])
    ->add(new ApiKeyMiddleware());


$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->get('/', [$homeController, 'index']);
$app->post('/api/logs/export', [$logController, 'export']);

$app->run();