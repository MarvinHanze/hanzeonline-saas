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

    /**
     * Verifieert email + wachtwoord + tenant, zonder de sessie al te "committen".
     * Nodig omdat 2FA-gebruikers na een correct wachtwoord eerst nog een
     * TOTP-code moeten invullen voordat de sessie volledig wordt opgebouwd.
     *
     * @return array|null De user-rij bij geldige combinatie, anders null.
     */
    public static function attempt(string $email, string $password, int $tenantId): ?array
    {
        $user = Database::fetch(
            "SELECT * FROM users WHERE email = ? AND tenant_id = ? LIMIT 1",
            [$email, $tenantId]
        );

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    /** True als deze user 2FA moet doorlopen vóór een volledige login. */
    public static function requiresTwoFactor(array $user): bool
    {
        return !empty($user['totp_enabled']);
    }

    /**
     * Zet een "pending" 2FA-sessie: wachtwoord was correct, maar de sessie is
     * nog niet volledig ingelogd totdat de TOTP-code is geverifieerd.
     */
    public static function beginTwoFactorChallenge(array $user, int $tenantId): void
    {
        self::start();
        $_SESSION['pending_2fa_user_id'] = (int) $user['id'];
        $_SESSION['pending_2fa_tenant_id'] = $tenantId;
    }

    public static function hasPendingTwoFactor(): bool
    {
        return isset($_SESSION['pending_2fa_user_id']);
    }

    public static function pendingTwoFactorUser(): ?array
    {
        if (!self::hasPendingTwoFactor()) {
            return null;
        }
        return Database::fetch(
            "SELECT * FROM users WHERE id = ? AND tenant_id = ?",
            [(int) $_SESSION['pending_2fa_user_id'], (int) $_SESSION['pending_2fa_tenant_id']]
        );
    }

    public static function clearPendingTwoFactor(): void
    {
        unset($_SESSION['pending_2fa_user_id'], $_SESSION['pending_2fa_tenant_id']);
    }

    /** Bouwt de volledige, ingelogde sessie op (na wachtwoord + evt. 2FA). */
    public static function establishSession(array $user, int $tenantId): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['tenant_id'] = $tenantId;
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        self::clearPendingTwoFactor();

        Database::update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
        Database::setTenant($tenantId);
        Permission::reset();
    }

    /**
     * Backwards-compatible helper: wachtwoord-login zonder 2FA-tussenstap.
     * Wordt binnen deze app alleen nog gebruikt als er geen 2FA is ingesteld;
     * de login-route in public/index.php checkt requiresTwoFactor() zelf.
     */
    public static function login(string $email, string $password, int $tenantId): bool
    {
        $user = self::attempt($email, $password, $tenantId);
        if (!$user) {
            return false;
        }
        if (self::requiresTwoFactor($user)) {
            self::beginTwoFactorChallenge($user, $tenantId);
            return false;
        }
        self::establishSession($user, $tenantId);
        return true;
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] > 0;
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE . '/login');
            exit;
        }
        // Tenant::load() haalt de volledige tenant-rij op (branding, plan, ...)
        // zodat die overal beschikbaar is, niet alleen op de '/'-route.
        Tenant::load((int) $_SESSION['tenant_id']);
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

    /**
     * Wachtwoord-sterkte-eis bij registratie/2FA-onafhankelijk wachtwoordbeheer:
     * min. 8 tekens + minstens één letter én één cijfer. Vervangt de eerdere
     * "minlength=8"-only client-side check door een echte server-side validatie.
     */
    public static function isStrongPassword(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[A-Za-z]/', $password) === 1
            && preg_match('/[0-9]/', $password) === 1;
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
            'status' => 'trialing',
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
