<?php
declare(strict_types=1);

namespace Core;

/**
 * Lichte RBAC-laag naast de bestaande single-role-kolom op `users`.
 * De rol (owner/admin/user) blijft de bron van waarheid op de sessie; deze
 * class koppelt rollen aan fijnmazigere permissie-keys via de
 * `permissions` + `role_permissions`-tabellen (zie Database::seedPermissions()).
 *
 * Gebruik: Permission::require('crm.manage') bovenaan een controller-actie,
 * of Permission::has('crm.manage') voor een if-check in een view.
 */
class Permission
{
    /** @var array<string,true>|null */
    private static ?array $cache = null;

    private static function loaded(string $role): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        self::$cache = [];
        if ($role === '') {
            return self::$cache;
        }

        try {
            $rows = Database::fetchAll(
                "SELECT p.`key` FROM role_permissions rp
                 JOIN permissions p ON p.id = rp.permission_id
                 WHERE rp.role = ?",
                [$role]
            );
        } catch (\Throwable $e) {
            // Tabellen bestaan mogelijk nog niet (bv. vlak na deploy vóór eerste
            // request Database::initSchema() heeft gedraaid) — fail-safe dicht.
            return self::$cache;
        }

        foreach ($rows as $row) {
            self::$cache[$row['key']] = true;
        }
        return self::$cache;
    }

    public static function has(string $permissionKey): bool
    {
        $role = (string) (Auth::user()['role'] ?? '');
        // Owner heeft altijd alles, ook als seeding nog niet is gedraaid.
        if ($role === 'owner') {
            return true;
        }
        return isset(self::loaded($role)[$permissionKey]);
    }

    /**
     * Blokkeert de request met 403 als de ingelogde gebruiker de permissie mist.
     */
    public static function require(string $permissionKey): void
    {
        if (!self::has($permissionKey)) {
            http_response_code(403);
            echo '<!DOCTYPE html><html lang="nl"><head><meta charset="UTF-8"><title>Geen toegang</title></head>'
                . '<body style="font-family:sans-serif;padding:3rem;text-align:center;color:#334155">'
                . '<h1>403 — Geen toegang</h1><p>Je account heeft niet de juiste rechten voor deze actie.</p>'
                . '<p><a href="' . BASE . '/dashboard">Terug naar dashboard</a></p></body></html>';
            exit;
        }
    }

    /** Reset de statische cache (handig na rolwijziging binnen dezelfde request). */
    public static function reset(): void
    {
        self::$cache = null;
    }
}
