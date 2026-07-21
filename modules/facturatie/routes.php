<?php declare(strict_types=1);
use Core\Router;
use Core\Auth;

Auth::requireLogin();

$router->group('/facturatie', function(Router $r) {
    $r->get('', [\Modules\Facturatie\Controllers\DashboardController::class, 'index']);
    $r->get('/klanten', [\Modules\Facturatie\Controllers\CustomerController::class, 'index']);
    $r->get('/klanten/nieuw', [\Modules\Facturatie\Controllers\CustomerController::class, 'create']);
    $r->post('/klanten', [\Modules\Facturatie\Controllers\CustomerController::class, 'store']);
    $r->get('/klanten/{id}', [\Modules\Facturatie\Controllers\CustomerController::class, 'show']);
    $r->get('/klanten/{id}/bewerk', [\Modules\Facturatie\Controllers\CustomerController::class, 'edit']);
    $r->post('/klanten/{id}', [\Modules\Facturatie\Controllers\CustomerController::class, 'update']);
    $r->post('/klanten/{id}/verwijder', [\Modules\Facturatie\Controllers\CustomerController::class, 'delete']);

    $r->get('/facturen', [\Modules\Facturatie\Controllers\InvoiceController::class, 'index']);
    $r->get('/facturen/nieuw', [\Modules\Facturatie\Controllers\InvoiceController::class, 'create']);
    $r->post('/facturen', [\Modules\Facturatie\Controllers\InvoiceController::class, 'store']);
    $r->get('/facturen/{id}', [\Modules\Facturatie\Controllers\InvoiceController::class, 'show']);
    $r->post('/facturen/{id}/status', [\Modules\Facturatie\Controllers\InvoiceController::class, 'updateStatus']);
    $r->get('/facturen/{id}/pdf', [\Modules\Facturatie\Controllers\InvoiceController::class, 'pdf']);
    $r->post('/facturen/{id}/herinnering', [\Modules\Facturatie\Controllers\InvoiceController::class, 'sendReminder']);
});
