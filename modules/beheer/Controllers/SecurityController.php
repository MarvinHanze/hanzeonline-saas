<?php
declare(strict_types=1);

namespace Modules\Beheer\Controllers;

use Core\Auth;
use Core\Database;
use Core\Totp;
use Core\View;

/**
 * 2FA (TOTP)-instellingen voor het EIGEN account. Bewust NIET achter
 * Permission::require('beheer.manage') — elke ingelogde gebruiker (owner,
 * admin, user) moet zijn eigen 2FA kunnen in-/uitschakelen, ongeacht rol.
 */
class SecurityController
{
    public function index(): void
    {
        $user = Auth::user();
        $fullUser = Database::fetch("SELECT * FROM users WHERE id = ?", [$user['id']]);

        $secret = $fullUser['totp_secret'] ?? null;
        $totpEnabled = (bool) ($fullUser['totp_enabled'] ?? false);

        if (!$totpEnabled && !$secret) {
            $secret = Totp::generateSecret();
            Database::update('users', ['totp_secret' => $secret], 'id = ?', [$user['id']]);
        }

        $flash = $_SESSION['beheer_flash'] ?? null;
        unset($_SESSION['beheer_flash']);

        View::render('modules/beheer/views/security/index', [
            'totpEnabled' => $totpEnabled,
            'secret' => $secret,
            'provisioningUri' => $secret ? Totp::provisioningUri($secret, $user['email']) : '',
            'formattedSecret' => $secret ? Totp::formatSecretForDisplay($secret) : '',
            'flash' => $flash,
        ]);
    }

    public function enable(): void
    {
        $user = Auth::user();
        $fullUser = Database::fetch("SELECT * FROM users WHERE id = ?", [$user['id']]);
        $code = trim($_POST['code'] ?? '');

        if (empty($fullUser['totp_secret']) || !Totp::verify((string) $fullUser['totp_secret'], $code)) {
            $_SESSION['beheer_flash'] = 'Ongeldige of verlopen verificatiecode. Probeer opnieuw.';
            header('Location: ' . BASE . '/beheer/beveiliging');
            exit;
        }

        Database::update('users', [
            'totp_enabled' => 1,
            'totp_confirmed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$user['id']]);

        $_SESSION['beheer_flash'] = 'Tweestapsverificatie is ingeschakeld voor je account.';
        header('Location: ' . BASE . '/beheer/beveiliging');
        exit;
    }

    public function disable(): void
    {
        $user = Auth::user();
        Database::update('users', [
            'totp_enabled' => 0,
            'totp_secret' => null,
            'totp_confirmed_at' => null,
        ], 'id = ?', [$user['id']]);

        $_SESSION['beheer_flash'] = 'Tweestapsverificatie is uitgeschakeld voor je account.';
        header('Location: ' . BASE . '/beheer/beveiliging');
        exit;
    }
}
