<?php declare(strict_types=1);
use Core\Router;
use Core\Auth;

Auth::requireLogin();

// De root-route ('/') roept DashboardController::index() al rechtstreeks aan
// vanuit public/index.php (kernmodule, altijd geladen). Na inloggen/2FA wordt
// er echter naar BASE . '/dashboard' geredirect (zie public/index.php), dus
// die route moet ook via de router bestaan.
$router->get('/dashboard', [\Modules\Dashboard\Controllers\DashboardController::class, 'index']);
