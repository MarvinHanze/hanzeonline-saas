<?php
declare(strict_types=1);

namespace Modules\Beheer\Controllers;

use Core\Auth;
use Core\Permission;
use Core\Plan;
use Core\Tenant;
use Core\View;

/**
 * Module-activatiescherm ("App Store"): toont de volledige catalogus uit
 * config/modules.php met per-tenant activatiestatus (tenant_modules) en
 * plan-gating (core/Plan.php). Een module die niet in het huidige abonnement
 * zit kan niet geactiveerd worden — de gebruiker krijgt een upgrade-melding
 * i.p.v. een activatie.
 */
class ModuleController
{
    public function index(): void
    {
        Permission::require('beheer.manage');

        $flash = $_SESSION['beheer_flash'] ?? null;
        unset($_SESSION['beheer_flash']);

        View::render('modules/beheer/views/modules/index', [
            'modules' => Tenant::modulesWithStatus(),
            'planSlug' => Tenant::plan(),
            'planLabel' => Plan::label(Tenant::plan()),
            'flash' => $flash,
        ]);
    }

    public function activate(string $key): void
    {
        Permission::require('beheer.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        $modules = Tenant::modulesWithStatus($tenantId);

        if (!isset($modules[$key])) {
            header('Location: ' . BASE . '/beheer/modules');
            exit;
        }

        // Placeholder-modules (esg/incidenten/integraties: "binnenkort beschikbaar")
        // hebben nog geen routes.php/Controllers — nooit activeerbaar, ook niet
        // via een handmatig verzoek, om een dode sidebar-link (404) te voorkomen.
        if (!empty($modules[$key]['placeholder']) || !file_exists(__DIR__ . "/../../../modules/$key/routes.php")) {
            $_SESSION['beheer_flash'] = 'De module "' . $modules[$key]['name'] . '" is nog niet beschikbaar.';
            header('Location: ' . BASE . '/beheer/modules');
            exit;
        }

        if (!$modules[$key]['allowed_by_plan']) {
            $_SESSION['beheer_flash'] = 'De module "' . $modules[$key]['name'] . '" zit niet in je huidige abonnement ('
                . Plan::label(Tenant::plan()) . '). Upgrade je abonnement via Beheer > Abonnement om deze te activeren.';
            header('Location: ' . BASE . '/beheer/modules');
            exit;
        }

        Tenant::setModuleEnabled($tenantId, $key, true);
        header('Location: ' . BASE . '/beheer/modules');
        exit;
    }

    public function deactivate(string $key): void
    {
        Permission::require('beheer.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        Tenant::setModuleEnabled($tenantId, $key, false);
        header('Location: ' . BASE . '/beheer/modules');
        exit;
    }
}
