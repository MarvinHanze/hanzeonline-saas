<?php
declare(strict_types=1);

namespace Core;

/**
 * Beheer van API-tokens voor de open REST-laag in api/v1 (partnerintegraties).
 * Het plaintext-token wordt NOOIT opgeslagen — alleen een sha256-hash. Het
 * plaintext-token wordt één keer getoond op het moment van aanmaken.
 */
class ApiToken
{
    public static function generate(int $tenantId, string $name): array
    {
        $plain = 'hz_' . bin2hex(random_bytes(32));
        $hash = hash('sha256', $plain);

        $id = Database::insert('api_tokens', [
            'tenant_id' => $tenantId,
            'name' => $name !== '' ? $name : 'API-token',
            'token_hash' => $hash,
        ]);

        return ['id' => $id, 'token' => $plain];
    }

    public static function verify(string $plainToken): ?array
    {
        if ($plainToken === '') {
            return null;
        }
        $hash = hash('sha256', $plainToken);
        return Database::fetch(
            "SELECT * FROM api_tokens WHERE token_hash = ? AND revoked_at IS NULL",
            [$hash]
        );
    }

    public static function touch(int $tokenId): void
    {
        Database::update('api_tokens', ['last_used_at' => date('Y-m-d H:i:s')], 'id = ?', [$tokenId]);
    }

    public static function revoke(int $tenantId, int $tokenId): void
    {
        Database::update(
            'api_tokens',
            ['revoked_at' => date('Y-m-d H:i:s')],
            'id = ? AND tenant_id = ?',
            [$tokenId, $tenantId]
        );
    }

    public static function forTenant(int $tenantId): array
    {
        return Database::fetchAll(
            "SELECT id, name, last_used_at, created_at, revoked_at
             FROM api_tokens WHERE tenant_id = ? ORDER BY created_at DESC",
            [$tenantId]
        );
    }
}
