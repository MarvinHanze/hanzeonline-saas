<?php
declare(strict_types=1);

namespace Core;

class Tenant
{
    private static ?array $current = null;

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

    public static function hasModule(string $module): bool
    {
        $modules = require __DIR__ . '/../config/modules.php';
        return ($modules[$module]['enabled'] ?? false);
    }

    public static function activeModules(): array
    {
        $modules = require __DIR__ . '/../config/modules.php';
        return array_filter($modules, fn($m) => $m['enabled']);
    }
}
