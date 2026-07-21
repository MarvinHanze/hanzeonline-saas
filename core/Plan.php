<?php
declare(strict_types=1);

namespace Core;

/**
 * Toegang tot config/plans.php — het "verdienmodel als code": per plan een
 * lijst toegestane modules + gebruikerslimiet. Geen echte betaalintegratie,
 * puur feature-gating + een UI-melding bij het activeren van een module die
 * niet in het huidige plan zit.
 */
class Plan
{
    private static ?array $plans = null;

    private static function all(): array
    {
        if (self::$plans === null) {
            self::$plans = require __DIR__ . '/../config/plans.php';
        }
        return self::$plans;
    }

    public static function get(string $planSlug): ?array
    {
        return self::all()[$planSlug] ?? null;
    }

    public static function list(): array
    {
        return self::all();
    }

    public static function allowsModule(string $planSlug, string $moduleKey): bool
    {
        $plan = self::get($planSlug);
        if (!$plan) {
            return false;
        }
        if ($plan['modules'] === '*') {
            return true;
        }
        return in_array($moduleKey, $plan['modules'], true);
    }

    public static function maxUsers(string $planSlug): ?int
    {
        $plan = self::get($planSlug);
        if (!$plan) {
            return null;
        }
        return $plan['max_users'];
    }

    public static function label(string $planSlug): string
    {
        return self::get($planSlug)['name'] ?? ucfirst($planSlug);
    }
}
