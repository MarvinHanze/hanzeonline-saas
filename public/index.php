<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Core\Auth;
use Core\Csrf;
use Core\Database;
use Core\ErrorHandler;
use Core\Router;
use Core\Tenant;
use Core\Totp;
use Core\View;

define('BASE', '/saas-platform');

// Load config vroeg (nodig voor de debug-vlag van de errorhandler).
$config = require __DIR__ . '/../config/app.php';

// Nooit ruwe PHP-fouten/stacktraces naar de browser in productie — altijd
// server-side loggen (zie core/ErrorHandler.php).
ErrorHandler::register((bool) ($config['debug'] ?? false));

// Sessie-cookie hardening: httpOnly (geen JS-toegang, XSS-mitigatie), Secure
// (site is alleen bereikbaar via https://demo.hanzeonline.nl), SameSite=Lax
// (CSRF-mitigatie in combinatie met Core\Csrf) en strict_mode (voorkomt
// session-fixation via een door de aanvaller aangeleverd session-ID).
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => BASE . '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Initialize database schema
Database::initSchema();

date_default_timezone_set($config['timezone']);
View::init(__DIR__ . '/..');

// Bootstrap
$router = new Router();

// Auth routes (no tenant required)
$router->get('/login', fn() => require __DIR__ . '/../core/views/login.php');
$router->post('/login', function () {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $tenantSlug = trim($_POST['tenant'] ?? '');

    if ($email === '' || $password === '' || $tenantSlug === '') {
        $error = 'Vul alle velden in.';
        require __DIR__ . '/../core/views/login.php';
        return;
    }

    // Brute-force-bescherming: max. 5 pogingen per 15 min. per account
    // (e-mail + bedrijf), ongeacht vanaf welk IP.
    $attemptId = 'login:' . strtolower($email) . '|' . strtolower($tenantSlug);
    if (Auth::isLockedOut($attemptId)) {
        $error = 'Te veel mislukte inlogpogingen. Probeer het over enkele minuten opnieuw.';
        require __DIR__ . '/../core/views/login.php';
        return;
    }

    $tenant = Database::fetch("SELECT * FROM tenants WHERE slug = ?", [$tenantSlug]);
    if (!$tenant) {
        Auth::recordFailedAttempt($attemptId);
        $error = 'Bedrijf niet gevonden.';
        require __DIR__ . '/../core/views/login.php';
        return;
    }

    $user = Auth::attempt($email, $password, (int) $tenant['id']);
    if (!$user) {
        Auth::recordFailedAttempt($attemptId);
        $error = 'Ongeldige inloggegevens.';
        require __DIR__ . '/../core/views/login.php';
        return;
    }

    Auth::clearFailedAttempts($attemptId);

    if (Auth::requiresTwoFactor($user)) {
        Auth::beginTwoFactorChallenge($user, (int) $tenant['id']);
        header('Location: ' . BASE . '/login/2fa');
        exit;
    }

    Auth::establishSession($user, (int) $tenant['id']);
    Tenant::load((int) $tenant['id']);
    header('Location: ' . BASE . '/dashboard');
    exit;
});

// Tweede stap van de login als het account 2FA (TOTP) heeft ingeschakeld.
$router->get('/login/2fa', function () {
    if (!Auth::hasPendingTwoFactor()) {
        header('Location: ' . BASE . '/login');
        exit;
    }
    require __DIR__ . '/../core/views/login_2fa.php';
});
$router->post('/login/2fa', function () {
    $pendingUser = Auth::pendingTwoFactorUser();
    if (!$pendingUser) {
        header('Location: ' . BASE . '/login');
        exit;
    }

    // Brute-force-bescherming op de TOTP-code (6 cijfers = maar 1 miljoen
    // combinaties, dus ook hier een poging-limiet nodig).
    $attemptId = '2fa:' . (int) $pendingUser['id'];
    if (Auth::isLockedOut($attemptId)) {
        $error = 'Te veel mislukte pogingen. Probeer het over enkele minuten opnieuw.';
        require __DIR__ . '/../core/views/login_2fa.php';
        return;
    }

    $code = trim($_POST['code'] ?? '');
    if (!Totp::verify((string) $pendingUser['totp_secret'], $code)) {
        Auth::recordFailedAttempt($attemptId);
        $error = 'Ongeldige of verlopen verificatiecode.';
        require __DIR__ . '/../core/views/login_2fa.php';
        return;
    }

    Auth::clearFailedAttempts($attemptId);

    $tenantId = (int) $_SESSION['pending_2fa_tenant_id'];
    Auth::establishSession($pendingUser, $tenantId);
    Tenant::load($tenantId);
    header('Location: ' . BASE . '/dashboard');
    exit;
});

// Zelfregistratie van een nieuwe tenant (14 dagen trial, plan 'starter').
$router->get('/register', function () {
    require __DIR__ . '/../core/views/register.php';
});
$router->post('/register', function () {
    $company = trim($_POST['company'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($company === '' || $name === '' || $email === '' || $password === '') {
        $error = 'Vul alle velden in.';
        require __DIR__ . '/../core/views/register.php';
        return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Vul een geldig e-mailadres in.';
        require __DIR__ . '/../core/views/register.php';
        return;
    }
    if (!Auth::isStrongPassword($password)) {
        $error = 'Wachtwoord moet minimaal 8 tekens bevatten, met minstens 1 letter en 1 cijfer.';
        require __DIR__ . '/../core/views/register.php';
        return;
    }
    if (Database::fetch("SELECT id FROM users WHERE email = ?", [$email])) {
        $error = 'Dit e-mailadres is al in gebruik.';
        require __DIR__ . '/../core/views/register.php';
        return;
    }

    $tenantId = Auth::registerTenant($company, $email, $password, $name);
    $user = Auth::attempt($email, $password, $tenantId);
    Auth::establishSession($user, $tenantId);
    Tenant::load($tenantId);

    header('Location: ' . BASE . '/beheer/onboarding');
    exit;
});

$router->get('/logout', function () {
    Auth::logout();
    header('Location: ' . BASE . '/login');
    exit;
});

// Protected root — stuurt door naar de nieuwe dashboard-module.
$router->get('/', function () {
    Auth::requireLogin();
    (new \Modules\Dashboard\Controllers\DashboardController())->index();
});

// Load module routes.
// Per-tenant module-activatie: kernmodules (dashboard/beheer) laden altijd,
// overige modules alleen als de ingelogde tenant ze heeft geactiveerd in
// tenant_modules (zie Core\Tenant::hasModule()). Een globale 'enabled'-vlag
// in config/modules.php blijft een ops-niveau kill-switch.
$modules = require __DIR__ . '/../config/modules.php';
foreach ($modules as $key => $module) {
    if (empty($module['enabled'])) {
        continue;
    }
    if (!file_exists(__DIR__ . "/../modules/$key/routes.php")) {
        continue;
    }
    if (!Auth::isLoggedIn()) {
        continue;
    }
    if (empty($module['core']) && !Tenant::hasModule($key)) {
        continue;
    }
    require __DIR__ . "/../modules/$key/routes.php";
}

// Dispatch
$router->dispatch();
