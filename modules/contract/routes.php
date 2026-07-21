<?php declare(strict_types=1);
use Core\Router;
use Core\Auth;

Auth::requireLogin();

$router->group('/contract', function(Router $r) {
    $r->get('', [\Modules\Contract\Controllers\DashboardController::class, 'index']);
    $r->get('/contracts', [\Modules\Contract\Controllers\ContractController::class, 'index']);
    $r->get('/contracts/nieuw', [\Modules\Contract\Controllers\ContractController::class, 'create']);
    $r->post('/contracts', [\Modules\Contract\Controllers\ContractController::class, 'store']);
    $r->get('/contracts/{id}', [\Modules\Contract\Controllers\ContractController::class, 'show']);
    $r->get('/contracts/{id}/pdf', [\Modules\Contract\Controllers\ContractController::class, 'pdf']);
    $r->post('/contracts/{id}/ondertekenen', [\Modules\Contract\Controllers\ContractController::class, 'sign']);
    $r->post('/contracts/{id}/status', [\Modules\Contract\Controllers\ContractController::class, 'updateStatus']);

    $r->get('/sjablonen', [\Modules\Contract\Controllers\TemplateController::class, 'index']);
    $r->get('/sjablonen/nieuw', [\Modules\Contract\Controllers\TemplateController::class, 'create']);
    $r->post('/sjablonen', [\Modules\Contract\Controllers\TemplateController::class, 'store']);
    $r->get('/sjablonen/{id}/bewerk', [\Modules\Contract\Controllers\TemplateController::class, 'edit']);
    $r->post('/sjablonen/{id}', [\Modules\Contract\Controllers\TemplateController::class, 'update']);
});
