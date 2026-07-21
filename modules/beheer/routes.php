<?php declare(strict_types=1);
use Core\Router;
use Core\Auth;

Auth::requireLogin();

$router->group('/beheer', function(Router $r) {
    $r->get('', [\Modules\Beheer\Controllers\ModuleController::class, 'index']);

    $r->get('/modules', [\Modules\Beheer\Controllers\ModuleController::class, 'index']);
    $r->post('/modules/{key}/activeren', [\Modules\Beheer\Controllers\ModuleController::class, 'activate']);
    $r->post('/modules/{key}/deactiveren', [\Modules\Beheer\Controllers\ModuleController::class, 'deactivate']);

    $r->get('/branding', [\Modules\Beheer\Controllers\BrandingController::class, 'index']);
    $r->post('/branding', [\Modules\Beheer\Controllers\BrandingController::class, 'update']);

    $r->get('/team', [\Modules\Beheer\Controllers\TeamController::class, 'index']);
    $r->post('/team', [\Modules\Beheer\Controllers\TeamController::class, 'store']);
    $r->post('/team/{id}/rol', [\Modules\Beheer\Controllers\TeamController::class, 'updateRole']);
    $r->post('/team/{id}/verwijderen', [\Modules\Beheer\Controllers\TeamController::class, 'delete']);

    $r->get('/api-tokens', [\Modules\Beheer\Controllers\ApiTokenController::class, 'index']);
    $r->post('/api-tokens', [\Modules\Beheer\Controllers\ApiTokenController::class, 'store']);
    $r->post('/api-tokens/{id}/intrekken', [\Modules\Beheer\Controllers\ApiTokenController::class, 'revoke']);

    $r->get('/beveiliging', [\Modules\Beheer\Controllers\SecurityController::class, 'index']);
    $r->post('/beveiliging/inschakelen', [\Modules\Beheer\Controllers\SecurityController::class, 'enable']);
    $r->post('/beveiliging/uitschakelen', [\Modules\Beheer\Controllers\SecurityController::class, 'disable']);

    $r->get('/abonnement', [\Modules\Beheer\Controllers\SubscriptionController::class, 'index']);
    $r->post('/abonnement', [\Modules\Beheer\Controllers\SubscriptionController::class, 'update']);

    $r->get('/onboarding', [\Modules\Beheer\Controllers\OnboardingController::class, 'index']);
    $r->post('/onboarding', [\Modules\Beheer\Controllers\OnboardingController::class, 'step']);
});
