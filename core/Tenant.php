<?php
declare(strict_types=1);

namespace Core;

class Tenant
{
    private static ?array $current = null;
    /** @var array<int,true> tenant-ids die deze request al geseed zijn, voorkomt herhaalde COUNT-query */
    private static array $seeded = [];

    public static function load(int $tenantId): void
    {
        self::$current = Database::fetch("SELECT * FROM tenants WHERE id = ?", [$tenantId]);
        Database::setTenant($tenantId);
    }

    public static function get(): ?array
    {
        return self::$current;
    }

    public static function id(): ?int
    {
        return self::$current['id'] ?? null;
    }

    public static function name(): string
    {
        return self::$current['name'] ?? '';
    }

    public static function slug(): string
    {
        return self::$current['slug'] ?? '';
    }

    public static function plan(): string
    {
        return self::$current['plan'] ?? 'starter';
    }

    public static function logoPath(): ?string
    {
        return self::$current['logo_path'] ?? null;
    }

    public static function brandColor(): string
    {
        return self::$current['brand_color'] ?? '#2563eb';
    }

    public static function locale(): string
    {
        return self::$current['locale'] ?? 'nl';
    }

    public static function onboardingStep(): int
    {
        return (int) (self::$current['onboarding_step'] ?? 0);
    }

    /**
     * Modulecatalogus uit config/modules.php (metadata: naam, icoon, categorie...),
     * ongeacht of een tenant de module heeft geactiveerd.
     */
    public static function catalog(): array
    {
        return require __DIR__ . '/../config/modules.php';
    }

    private static function resolveTenantId(?int $tenantId): int
    {
        return $tenantId ?? self::id() ?? (int) ($_SESSION['tenant_id'] ?? 0);
    }

    /**
     * Zorgt dat een tenant bij eerste gebruik de historische default-modules
     * (facturatie/hr/contract, die vóór de SaaS-uitbreiding altijd globaal aan
     * stonden) geactiveerd krijgt in tenant_modules. Idempotent en per-request
     * gecached zodat dit niet bij elke hasModule()-call opnieuw telt.
     */
    private static function ensureSeeded(int $tenantId): void
    {
        if ($tenantId <= 0 || isset(self::$seeded[$tenantId])) {
            return;
        }
        self::$seeded[$tenantId] = true;

        if (!Database::tableExists('tenant_modules')) {
            return;
        }

        $count = Database::count('tenant_modules', 'tenant_id = ?', [$tenantId]);
        if ($count > 0) {
            return;
        }

        $catalog = self::catalog();
        $defaults = ['facturatie', 'hr', 'contract'];
        foreach ($defaults as $key) {
            if (!isset($catalog[$key])) {
                continue;
            }
            Database::insert('tenant_modules', [
                'tenant_id' => $tenantId,
                'module_key' => $key,
                'enabled' => 1,
                'enabled_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Is $module actief voor deze tenant? Kernmodules (dashboard/beheer) staan
     * altijd aan. Overige modules worden per-tenant bepaald door tenant_modules.
     */
    public static function hasModule(string $module, ?int $tenantId = null): bool
    {
        $catalog = self::catalog();
        if (empty($catalog[$module]['enabled'])) {
            return false; // globale kill-switch
        }
        if (!empty($catalog[$module]['core'])) {
            return true;
        }

        $tenantId = self::resolveTenantId($tenantId);
        if ($tenantId <= 0) {
            return false;
        }
        self::ensureSeeded($tenantId);

        $row = Database::fetch(
            "SELECT enabled FROM tenant_modules WHERE tenant_id = ? AND module_key = ?",
            [$tenantId, $module]
        );
        return (bool) ($row['enabled'] ?? false);
    }

    /**
     * Actieve modules (incl. kernmodules) met metadata, in catalogusvolgorde —
     * gebruikt voor sidebar-navigatie en de module-kaarten op het dashboard.
     */
    public static function activeModules(?int $tenantId = null): array
    {
        $catalog = self::catalog();
        $tenantId = self::resolveTenantId($tenantId);
        self::ensureSeeded($tenantId);

        $enabledKeys = [];
        if ($tenantId > 0 && Database::tableExists('tenant_modules')) {
            $rows = Database::fetchAll(
                "SELECT module_key FROM tenant_modules WHERE tenant_id = ? AND enabled = 1",
                [$tenantId]
            );
            $enabledKeys = array_column($rows, 'module_key');
        }

        $active = [];
        foreach ($catalog as $key => $meta) {
            if (empty($meta['enabled'])) {
                continue;
            }
            if (!empty($meta['core']) || in_array($key, $enabledKeys, true)) {
                $active[$key] = $meta;
            }
        }
        return $active;
    }

    /**
     * Volledige catalogus + per-tenant activatiestatus + plan-toestemming —
     * gebruikt door de module-store (Beheer > Modules).
     */
    public static function modulesWithStatus(?int $tenantId = null): array
    {
        $catalog = self::catalog();
        $tenantId = self::resolveTenantId($tenantId);
        self::ensureSeeded($tenantId);

        $enabledKeys = [];
        if ($tenantId > 0 && Database::tableExists('tenant_modules')) {
            $rows = Database::fetchAll(
                "SELECT module_key FROM tenant_modules WHERE tenant_id = ? AND enabled = 1",
                [$tenantId]
            );
            $enabledKeys = array_column($rows, 'module_key');
        }

        $planSlug = self::plan();
        $result = [];
        foreach ($catalog as $key => $meta) {
            if (empty($meta['enabled'])) {
                continue;
            }
            $result[$key] = $meta + [
                'is_core' => !empty($meta['core']),
                'is_enabled' => !empty($meta['core']) || in_array($key, $enabledKeys, true),
                'allowed_by_plan' => Plan::allowsModule($planSlug, $key),
            ];
        }
        return $result;
    }

    /** Zet een module aan/uit voor de huidige tenant (module-store actie). */
    public static function setModuleEnabled(int $tenantId, string $moduleKey, bool $enabled): void
    {
        $existing = Database::fetch(
            "SELECT id FROM tenant_modules WHERE tenant_id = ? AND module_key = ?",
            [$tenantId, $moduleKey]
        );
        if ($existing) {
            Database::update(
                'tenant_modules',
                ['enabled' => $enabled ? 1 : 0, 'enabled_at' => $enabled ? date('Y-m-d H:i:s') : null],
                'id = ?',
                [$existing['id']]
            );
        } else {
            Database::insert('tenant_modules', [
                'tenant_id' => $tenantId,
                'module_key' => $moduleKey,
                'enabled' => $enabled ? 1 : 0,
                'enabled_at' => $enabled ? date('Y-m-d H:i:s') : null,
            ]);
        }
    }
}
