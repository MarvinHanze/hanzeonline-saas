<?php declare(strict_types=1);
use Core\Router;
use Core\Auth;

Auth::requireLogin();

$router->group('/hr', function(Router $r) {
    $r->get('', [\Modules\Hr\Controllers\DashboardController::class, 'index']);
    $r->get('/medewerkers', [\Modules\Hr\Controllers\EmployeeController::class, 'index']);
    $r->get('/medewerkers/nieuw', [\Modules\Hr\Controllers\EmployeeController::class, 'create']);
    $r->post('/medewerkers', [\Modules\Hr\Controllers\EmployeeController::class, 'store']);
    $r->get('/medewerkers/{id}', [\Modules\Hr\Controllers\EmployeeController::class, 'show']);
    $r->get('/medewerkers/{id}/bewerk', [\Modules\Hr\Controllers\EmployeeController::class, 'edit']);
    $r->post('/medewerkers/{id}', [\Modules\Hr\Controllers\EmployeeController::class, 'update']);

    $r->get('/verlof', [\Modules\Hr\Controllers\LeaveController::class, 'index']);
    $r->post('/verlof', [\Modules\Hr\Controllers\LeaveController::class, 'store']);
    $r->post('/verlof/{id}/goedkeuren', [\Modules\Hr\Controllers\LeaveController::class, 'approve']);
    $r->post('/verlof/{id}/afwijzen', [\Modules\Hr\Controllers\LeaveController::class, 'reject']);

    $r->get('/organogram', [\Modules\Hr\Controllers\DepartmentController::class, 'index']);
    $r->get('/beoordelingen', [\Modules\Hr\Controllers\ReviewController::class, 'index']);
});
