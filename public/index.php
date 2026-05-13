<?php

use App\Controllers\HomeController;
use App\Controllers\LogController;
use App\Models\Log;
use App\Services\DatabaseService;
use App\Services\LogService;
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

$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->get('/', [$homeController, 'index']);
$app->post('/api/logs/export', [$logController, 'export']);

$app->run();