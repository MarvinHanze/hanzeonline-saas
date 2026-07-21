<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Core\Database;
use Core\Auth;
use Core\Router;
use Core\Tenant;
use Core\View;

define('BASE', '/saas-platform');
session_start();

// Initialize database schema
Database::initSchema();

// Load config
$config = require __DIR__ . '/../config/app.php';
date_default_timezone_set($config['timezone']);
View::init(__DIR__ . '/..');

// Bootstrap
$router = new Router();

// Auth routes (no tenant required)
$router->get('/login', fn() => require __DIR__ . '/../core/views/login.php');
$router->post('/login', function() {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $tenantSlug = trim($_POST['tenant'] ?? '');

    if ($email === '' || $password === '' || $tenantSlug === '') {
        $error = 'Vul alle velden in.';
        require __DIR__ . '/../core/views/login.php';
        return;
    }

    $tenant = Database::fetch("SELECT * FROM tenants WHERE slug = ?", [$tenantSlug]);
    if (!$tenant) {
        $error = 'Bedrijf niet gevonden.';
        require __DIR__ . '/../core/views/login.php';
        return;
    }

    if (Auth::login($email, $password, (int) $tenant['id'])) {
        Tenant::load((int) $tenant['id']);
        header('Location: ' . BASE . '/');
        exit;
    }

    $error = 'Ongeldige inloggegevens.';
    require __DIR__ . '/../core/views/login.php';
});

$router->get('/register', function() { header('Location: ' . BASE . '/login'); exit; });
$router->post('/register', function() { header('Location: ' . BASE . '/login'); exit; });

$router->get('/logout', function() {
    Auth::logout();
    header('Location: ' . BASE . '/login');
    exit;
});

// Protected routes — require auth
$router->get('/', function() {
    Auth::requireLogin();
    Tenant::load((int) $_SESSION['tenant_id']);
    require __DIR__ . '/../core/views/dashboard.php';
});

// Load module routes
$modules = require __DIR__ . '/../config/modules.php';
foreach ($modules as $key => $module) {
    if ($module['enabled'] && file_exists(__DIR__ . "/../modules/$key/routes.php") && Auth::isLoggedIn()) {
        require __DIR__ . "/../modules/$key/routes.php";
    }
}

// Dispatch
$router->dispatch();
