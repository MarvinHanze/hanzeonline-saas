<?php declare(strict_types=1);
use Core\Router;
use Core\Auth;

Auth::requireLogin();

$router->group('/crm', function(Router $r) {
    $r->get('', [\Modules\Crm\Controllers\DashboardController::class, 'index']);

    $r->get('/leads', [\Modules\Crm\Controllers\LeadController::class, 'index']);
    $r->get('/leads/nieuw', [\Modules\Crm\Controllers\LeadController::class, 'create']);
    $r->post('/leads', [\Modules\Crm\Controllers\LeadController::class, 'store']);
    $r->get('/leads/{id}', [\Modules\Crm\Controllers\LeadController::class, 'show']);
    $r->get('/leads/{id}/bewerk', [\Modules\Crm\Controllers\LeadController::class, 'edit']);
    $r->post('/leads/{id}', [\Modules\Crm\Controllers\LeadController::class, 'update']);

    $r->get('/offertes', [\Modules\Crm\Controllers\QuoteController::class, 'index']);
    $r->get('/offertes/nieuw', [\Modules\Crm\Controllers\QuoteController::class, 'create']);
    $r->post('/offertes', [\Modules\Crm\Controllers\QuoteController::class, 'store']);
    $r->post('/offertes/{id}/status', [\Modules\Crm\Controllers\QuoteController::class, 'updateStatus']);

    $r->get('/orders', [\Modules\Crm\Controllers\OrderController::class, 'index']);
    $r->get('/orders/nieuw', [\Modules\Crm\Controllers\OrderController::class, 'create']);
    $r->post('/orders', [\Modules\Crm\Controllers\OrderController::class, 'store']);
    $r->post('/orders/{id}/status', [\Modules\Crm\Controllers\OrderController::class, 'updateStatus']);
});
