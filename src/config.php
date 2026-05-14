<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

function connectDatabase(): PDO
{
    $host = $_ENV['DB_HOST'] ?? 'db';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $database = $_ENV['MYSQL_DATABASE'];
    $username = $_ENV['MYSQL_USER'];
    $password = $_ENV['MYSQL_PASSWORD'];

    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
}

function getApiKey(): string
{
    return $_ENV['API_KEY'];
}

function getCasDelayMs(): int
{
    return (int) ($_ENV['CAS_DELAY_MS'] ?? 0);
}