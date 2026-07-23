<?php
declare(strict_types=1);

/**
 * Front controller voor de open REST-API (partnerintegraties). Losstaand van
 * public/index.php: geen sessie, geen Auth::-cookie-login — authenticatie
 * gebeurt per-request via een Bearer API-token (core/ApiToken.php, beheerd
 * via Beheer > API-tokens). public/.htaccess stuurt elk /api/...-verzoek
 * hierheen.
 */

require __DIR__ . '/../../vendor/autoload.php';

use Core\ApiToken;
use Core\Database;
use Core\ErrorHandler;

header('Content-Type: application/json; charset=utf-8');

$appConfig = require __DIR__ . '/../../config/app.php';
ErrorHandler::register((bool) ($appConfig['debug'] ?? false));

Database::initSchema();

// --- Authenticatie ---
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if ($authHeader === '' && function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? ($headers['authorization'] ?? '');
}

if (!preg_match('/^Bearer\s+(.+)$/i', trim($authHeader), $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'Ontbrekende of ongeldige Authorization-header. Gebruik: Authorization: Bearer <token>']);
    exit;
}

$tokenRow = ApiToken::verify(trim($matches[1]));
if (!$tokenRow) {
    http_response_code(401);
    echo json_encode(['error' => 'Ongeldig of ingetrokken API-token']);
    exit;
}

ApiToken::touch((int) $tokenRow['id']);
$tenantId = (int) $tokenRow['tenant_id'];
Database::setTenant($tenantId);

// --- Routing (kleine variant van Core\Router, los van de sessie-gebonden webrouter) ---
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim((string) $uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    ['GET', '/api/v1/facturen', [\Api\V1\Controllers\FacturenController::class, 'index']],
    ['GET', '/api/v1/facturen/{id}', [\Api\V1\Controllers\FacturenController::class, 'show']],
    ['GET', '/api/v1/klanten', [\Api\V1\Controllers\KlantenController::class, 'index']],
    ['GET', '/api/v1/klanten/{id}', [\Api\V1\Controllers\KlantenController::class, 'show']],
];

foreach ($routes as [$routeMethod, $routePath, $handler]) {
    if ($routeMethod !== $method) {
        continue;
    }
    $regex = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $routePath) . '$#';
    if (preg_match($regex, $uri, $paramMatches)) {
        $params = array_filter($paramMatches, 'is_string', ARRAY_FILTER_USE_KEY);
        [$class, $methodName] = $handler;
        (new $class($tenantId))->$methodName(...array_values($params));
        exit;
    }
}

http_response_code(404);
echo json_encode(['error' => 'Onbekend API-endpoint']);
