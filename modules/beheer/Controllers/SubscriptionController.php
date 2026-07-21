<?php
declare(strict_types=1);

namespace Modules\Beheer\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\Plan;
use Core\Tenant;
use Core\View;

/**
 * Pricing-tiers (config/plans.php) als "verdienmodel als code". Geen echte
 * Mollie/Stripe-integratie — plan-wijziging is een gelabelde demo-simulatie.
 */
class SubscriptionController
{
    public function index(): void
    {
        Permission::require('beheer.manage');

        $flash = $_SESSION['beheer_flash'] ?? null;
        unset($_SESSION['beheer_flash']);

        View::render('modules/beheer/views/subscription/index', [
            'plans' => Plan::list(),
            'currentSlug' => Tenant::plan(),
            'currentUsers' => Database::count('users', 'tenant_id = ?', [Tenant::id()]),
            'flash' => $flash,
        ]);
    }

    public function update(): void
    {
        Permission::require('beheer.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        $slug = (string) ($_POST['plan'] ?? '');

        if (!Plan::get($slug)) {
            header('Location: ' . BASE . '/beheer/abonnement');
            exit;
        }

        Database::update('tenants', ['plan' => $slug], 'id = ?', [$tenantId]);
        Database::insert('subscriptions', [
            'tenant_id' => $tenantId,
            'plan' => $slug,
            'status' => 'active',
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 month')),
        ]);
        Tenant::load($tenantId);

        $_SESSION['beheer_flash'] = 'Abonnement gewijzigd naar ' . Plan::label($slug) . ' (demo-simulatie — geen echte betaling verwerkt).';
        header('Location: ' . BASE . '/beheer/abonnement');
        exit;
    }
}
