<?php
declare(strict_types=1);

namespace Modules\Beheer\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\Plan;
use Core\Tenant;
use Core\View;

class TeamController
{
    public function index(): void
    {
        Permission::require('beheer.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $members = Database::fetchAll(
            "SELECT id, name, email, role, last_login, created_at FROM users WHERE tenant_id = ? ORDER BY created_at ASC",
            [$tenantId]
        );
        $plan = Plan::get(Tenant::plan());

        $flash = $_SESSION['beheer_flash'] ?? null;
        unset($_SESSION['beheer_flash']);

        View::render('modules/beheer/views/team/index', [
            'members' => $members,
            'maxUsers' => $plan['max_users'] ?? null,
            'currentUserId' => (int) Auth::user()['id'],
            'flash' => $flash,
        ]);
    }

    public function store(): void
    {
        Permission::require('beheer.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $plan = Plan::get(Tenant::plan());
        $maxUsers = $plan['max_users'] ?? null;
        if ($maxUsers !== null) {
            $current = Database::count('users', 'tenant_id = ?', [$tenantId]);
            if ($current >= $maxUsers) {
                $_SESSION['beheer_flash'] = 'Je hebt het maximale aantal gebruikers (' . $maxUsers
                    . ') voor je abonnement bereikt. Upgrade je abonnement om meer teamleden toe te voegen.';
                header('Location: ' . BASE . '/beheer/team');
                exit;
            }
        }

        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $role = in_array($_POST['role'] ?? '', ['admin', 'user'], true) ? $_POST['role'] : 'user';
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !Auth::isStrongPassword($password)) {
            $_SESSION['beheer_flash'] = 'Vul een naam, geldig e-mailadres en een wachtwoord van min. 8 tekens (met letter + cijfer) in.';
            header('Location: ' . BASE . '/beheer/team');
            exit;
        }
        if (Database::fetch("SELECT id FROM users WHERE email = ? AND tenant_id = ?", [$email, $tenantId])) {
            $_SESSION['beheer_flash'] = 'Dit e-mailadres is al in gebruik binnen dit team.';
            header('Location: ' . BASE . '/beheer/team');
            exit;
        }

        Database::insert('users', [
            'tenant_id' => $tenantId,
            'email' => $email,
            'password' => Auth::hashPassword($password),
            'name' => $name,
            'role' => $role,
        ]);

        header('Location: ' . BASE . '/beheer/team');
        exit;
    }

    public function updateRole(string $id): void
    {
        Permission::require('beheer.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        $role = in_array($_POST['role'] ?? '', ['owner', 'admin', 'user'], true) ? $_POST['role'] : 'user';

        if ($role !== 'owner') {
            $ownerCount = Database::count('users', "tenant_id = ? AND role = 'owner'", [$tenantId]);
            $target = Database::fetch("SELECT role FROM users WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
            if ($target && $target['role'] === 'owner' && $ownerCount <= 1) {
                $_SESSION['beheer_flash'] = 'De laatste owner van dit account kan niet worden gedegradeerd.';
                header('Location: ' . BASE . '/beheer/team');
                exit;
            }
        }

        Database::update('users', ['role' => $role], 'id = ? AND tenant_id = ?', [(int) $id, $tenantId]);
        header('Location: ' . BASE . '/beheer/team');
        exit;
    }

    public function delete(string $id): void
    {
        Permission::require('beheer.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        $currentUserId = (int) Auth::user()['id'];

        if ((int) $id === $currentUserId) {
            $_SESSION['beheer_flash'] = 'Je kunt jezelf niet verwijderen uit het team.';
            header('Location: ' . BASE . '/beheer/team');
            exit;
        }

        Database::delete('users', 'id = ? AND tenant_id = ?', [(int) $id, $tenantId]);
        header('Location: ' . BASE . '/beheer/team');
        exit;
    }
}
