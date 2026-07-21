<?php declare(strict_types=1);
use Core\Router;
use Core\Auth;

Auth::requireLogin();

$router->group('/voorraad', function(Router $r) {
    $r->get('', [\Modules\Voorraad\Controllers\DashboardController::class, 'index']);

    $r->get('/producten', [\Modules\Voorraad\Controllers\ProductController::class, 'index']);
    $r->get('/producten/nieuw', [\Modules\Voorraad\Controllers\ProductController::class, 'create']);
    $r->post('/producten', [\Modules\Voorraad\Controllers\ProductController::class, 'store']);
    $r->get('/producten/{id}', [\Modules\Voorraad\Controllers\ProductController::class, 'show']);
    $r->get('/producten/{id}/bewerk', [\Modules\Voorraad\Controllers\ProductController::class, 'edit']);
    $r->post('/producten/{id}', [\Modules\Voorraad\Controllers\ProductController::class, 'update']);
    $r->post('/producten/{id}/voorraad', [\Modules\Voorraad\Controllers\ProductController::class, 'adjustStock']);

    $r->get('/magazijnen', [\Modules\Voorraad\Controllers\WarehouseController::class, 'index']);
    $r->post('/magazijnen', [\Modules\Voorraad\Controllers\WarehouseController::class, 'store']);

    $r->get('/inkooporders', [\Modules\Voorraad\Controllers\PurchaseOrderController::class, 'index']);
    $r->get('/inkooporders/nieuw', [\Modules\Voorraad\Controllers\PurchaseOrderController::class, 'create']);
    $r->post('/inkooporders', [\Modules\Voorraad\Controllers\PurchaseOrderController::class, 'store']);
    $r->get('/inkooporders/{id}', [\Modules\Voorraad\Controllers\PurchaseOrderController::class, 'show']);
    $r->post('/inkooporders/{id}/status', [\Modules\Voorraad\Controllers\PurchaseOrderController::class, 'updateStatus']);

    $r->get('/materieel', [\Modules\Voorraad\Controllers\EquipmentController::class, 'index']);
    $r->post('/materieel', [\Modules\Voorraad\Controllers\EquipmentController::class, 'store']);
    $r->post('/materieel/{id}/status', [\Modules\Voorraad\Controllers\EquipmentController::class, 'updateStatus']);
});
