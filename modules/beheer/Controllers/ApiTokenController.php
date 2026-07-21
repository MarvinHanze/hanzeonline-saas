<?php
declare(strict_types=1);

namespace Modules\Beheer\Controllers;

use Core\ApiToken;
use Core\Auth;
use Core\Permission;
use Core\View;

class ApiTokenController
{
    public function index(): void
    {
        Permission::require('api.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $newToken = $_SESSION['new_api_token'] ?? null;
        unset($_SESSION['new_api_token']);

        View::render('modules/beheer/views/api_tokens/index', [
            'tokens' => ApiToken::forTenant($tenantId),
            'newToken' => $newToken,
        ]);
    }

    public function store(): void
    {
        Permission::require('api.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        $name = trim($_POST['name'] ?? '');

        $result = ApiToken::generate($tenantId, $name);
        $_SESSION['new_api_token'] = $result['token'];

        header('Location: ' . BASE . '/beheer/api-tokens');
        exit;
    }

    public function revoke(string $id): void
    {
        Permission::require('api.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        ApiToken::revoke($tenantId, (int) $id);

        header('Location: ' . BASE . '/beheer/api-tokens');
        exit;
    }
}
