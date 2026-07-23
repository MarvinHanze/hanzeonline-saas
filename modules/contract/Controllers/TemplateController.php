<?php
declare(strict_types=1);

namespace Modules\Contract\Controllers;

use Core\Database;
use Core\Auth;
use Core\Permission;
use Core\View;

class TemplateController
{
    public function index(): void
    {
        Permission::require('contract.view');
        $tenantId = Auth::user()['tenant_id'];

        $templates = Database::fetchAll(
            "SELECT t.*, (SELECT COUNT(*) FROM ct_contracts c WHERE c.template_id = t.id) as usage_count
             FROM ct_templates t WHERE t.tenant_id = ? ORDER BY t.name",
            [$tenantId]
        );

        View::render('modules/contract/views/templates/index', [
            'templates' => $templates,
        ]);
    }

    public function create(): void
    {
        Permission::require('contract.manage');
        View::render('modules/contract/views/templates/create');
    }

    public function store(): void
    {
        Permission::require('contract.manage');
        $tenantId = Auth::user()['tenant_id'];

        Database::insert('ct_templates', [
            'tenant_id' => $tenantId,
            'name' => trim($_POST['name'] ?? ''),
            'content_html' => $_POST['content_html'] ?? '',
            'variables' => json_encode($this->extractVariables($_POST['content_html'] ?? '')),
        ]);

        header('Location: ' . BASE . '/contract/sjablonen');
        exit;
    }

    public function edit(string $id): void
    {
        Permission::require('contract.manage');
        $tenantId = Auth::user()['tenant_id'];

        $template = Database::fetch(
            "SELECT * FROM ct_templates WHERE id = ? AND tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$template) {
            http_response_code(404);
            echo 'Sjabloon niet gevonden';
            return;
        }

        View::render('modules/contract/views/templates/edit', [
            'template' => $template,
        ]);
    }

    public function update(string $id): void
    {
        Permission::require('contract.manage');
        $tenantId = Auth::user()['tenant_id'];

        Database::update('ct_templates', [
            'name' => trim($_POST['name'] ?? ''),
            'content_html' => $_POST['content_html'] ?? '',
            'variables' => json_encode($this->extractVariables($_POST['content_html'] ?? '')),
        ], 'id = ? AND tenant_id = ?', [$id, $tenantId]);

        header('Location: ' . BASE . '/contract/sjablonen');
        exit;
    }

    private function extractVariables(string $html): array
    {
        preg_match_all('/\{\{(\w+)\}\}/', $html, $matches);
        return array_unique($matches[1] ?? []);
    }
}
