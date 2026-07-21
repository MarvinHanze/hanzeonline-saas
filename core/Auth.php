<?php
declare(strict_types=1);

namespace Core;

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(string $email, string $password, int $tenantId): bool
    {
        $user = Database::fetch(
            "SELECT * FROM users WHERE email = ? AND tenant_id = ? LIMIT 1",
            [$email, $tenantId]
        );

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['tenant_id'] = $tenantId;
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            Database::update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
            Database::setTenant($tenantId);

            return true;
        }
        return false;
    }

    public static function logout(): void
    {
        session_destroy();
        $_SESSION = [];
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] > 0;
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        Database::setTenant((int) $_SESSION['tenant_id']);
    }

    public static function user(): array
    {
        return [
            'id' => (int) ($_SESSION['user_id'] ?? 0),
            'tenant_id' => (int) ($_SESSION['tenant_id'] ?? 0),
            'email' => (string) ($_SESSION['user_email'] ?? ''),
            'name' => (string) ($_SESSION['user_name'] ?? ''),
            'role' => (string) ($_SESSION['user_role'] ?? ''),
        ];
    }

    public static function isOwner(): bool
    {
        return ($_SESSION['user_role'] ?? '') === 'owner';
    }

    public static function isAdmin(): bool
    {
        return in_array($_SESSION['user_role'] ?? '', ['owner', 'admin']);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function registerTenant(string $companyName, string $email, string $password, string $name): int
    {
        $slug = self::generateSlug($companyName);

        $tenantId = Database::insert('tenants', [
            'name' => $companyName,
            'slug' => $slug,
            'plan' => 'starter',
        ]);

        Database::insert('users', [
            'tenant_id' => $tenantId,
            'email' => $email,
            'password' => self::hashPassword($password),
            'name' => $name,
            'role' => 'owner',
        ]);

        Database::insert('subscriptions', [
            'tenant_id' => $tenantId,
            'plan' => 'starter',
            'status' => 'active',
            'expires_at' => date('Y-m-d H:i:s', strtotime('+14 days')),
        ]);

        return $tenantId;
    }

    private static function generateSlug(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        $base = $slug;
        $i = 1;
        while (Database::fetch("SELECT id FROM tenants WHERE slug = ?", [$slug])) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
