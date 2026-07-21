<?php declare(strict_types=1);
use Core\Router;
use Core\Auth;

Auth::requireLogin();

$router->group('/projecten', function(Router $r) {
    $r->get('', [\Modules\Projecten\Controllers\DashboardController::class, 'index']);

    $r->get('/projecten', [\Modules\Projecten\Controllers\ProjectController::class, 'index']);
    $r->get('/projecten/nieuw', [\Modules\Projecten\Controllers\ProjectController::class, 'create']);
    $r->post('/projecten', [\Modules\Projecten\Controllers\ProjectController::class, 'store']);
    $r->get('/projecten/{id}', [\Modules\Projecten\Controllers\ProjectController::class, 'show']);
    $r->get('/projecten/{id}/bewerk', [\Modules\Projecten\Controllers\ProjectController::class, 'edit']);
    $r->post('/projecten/{id}', [\Modules\Projecten\Controllers\ProjectController::class, 'update']);

    $r->post('/projecten/{id}/taken', [\Modules\Projecten\Controllers\TaskController::class, 'store']);
    $r->post('/taken/{id}/status', [\Modules\Projecten\Controllers\TaskController::class, 'updateStatus']);

    $r->post('/projecten/{id}/uren', [\Modules\Projecten\Controllers\TimeEntryController::class, 'store']);
    $r->get('/uren', [\Modules\Projecten\Controllers\TimeEntryController::class, 'index']);
});
