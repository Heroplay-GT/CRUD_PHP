<?php

declare(strict_types=1);

/**
 * Paso 15 del material: único entrypoint HTTP.
 * Paso 12: Common (ClassLoader + DependencyInjection) antes de despachar al controlador.
 */

require dirname(__DIR__) . '/Common/ClassLoader.php';
require dirname(__DIR__) . '/Common/DependencyInjection.php';
QA\Common\DependencyInjection::boot(dirname(__DIR__));

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

use QA\Common\DependencyInjection;
use QA\Infrastructure\Entrypoints\Web\Controllers\Config\WebRoutes;
use QA\Infrastructure\Entrypoints\Web\Presentation\Flash;

Flash::start();

$route = isset($_GET['route']) ? (string) $_GET['route'] : null;
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$match = WebRoutes::match($route, is_string($method) ? $method : 'GET');

if ($match === null) {
    http_response_code(404);
    echo 'Ruta o método no permitido.';
    exit;
}

$routeName = $match['route'];
if (!in_array($routeName, WebRoutes::publicRouteNames(), true) && !isset($_SESSION['auth']['id'])) {
    header('Location: index.php?route=auth.login');
    exit;
}

$controller = DependencyInjection::getUserController();

[, $action] = explode('@', $match['action'], 2);
$controller->{$action}();
