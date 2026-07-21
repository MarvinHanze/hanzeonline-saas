<?php
declare(strict_types=1);

namespace Modules\Beheer\Controllers;

use Core\Auth;
use Core\Database;
use Core\Plan;
use Core\Tenant;
use Core\View;

/**
 * Korte onboarding-wizard direct na zelfregistratie (public/index.php
 * /register redirect hierheen). 3 stappen: branding-kleur, modules kiezen,
 * klaar. Voortgang staat in tenants.onboarding_step.
 */
class OnboardingController
{
    private const TOTAL_STEPS = 3;

    public function index(): void
    {
        $step = Tenant::onboardingStep();
        if ($step >= self::TOTAL_STEPS) {
            header('Location: ' . BASE . '/dashboard');
            exit;
        }

        View::render('modules/beheer/views/onboarding/index', [
            'step' => $step,
            'totalSteps' => self::TOTAL_STEPS,
            'tenant' => Tenant::get(),
            'modules' => Tenant::modulesWithStatus(),
        ]);
    }

    public function step(): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];
        $current = Tenant::onboardingStep();

        if ($current === 0) {
            $color = trim($_POST['brand_color'] ?? '');
            if (preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                Database::update('tenants', ['brand_color' => $color], 'id = ?', [$tenantId]);
            }
        } elseif ($current === 1) {
            $selected = $_POST['modules'] ?? [];
            if (is_array($selected)) {
                foreach ($selected as $key) {
                    $key = (string) $key;
                    // Alleen echt gebouwde, niet-placeholder modules die het plan toestaat.
                    if (Plan::allowsModule(Tenant::plan(), $key) && file_exists(__DIR__ . "/../../../modules/$key/routes.php")) {
                        Tenant::setModuleEnabled($tenantId, $key, true);
                    }
                }
            }
        }

        $next = min($current + 1, self::TOTAL_STEPS);
        Database::update('tenants', ['onboarding_step' => $next], 'id = ?', [$tenantId]);
        Tenant::load($tenantId);

        header('Location: ' . BASE . ($next >= self::TOTAL_STEPS ? '/dashboard' : '/beheer/onboarding'));
        exit;
    }
}
