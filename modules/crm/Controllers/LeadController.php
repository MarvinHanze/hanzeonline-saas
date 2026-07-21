<?php
declare(strict_types=1);

namespace Modules\Crm\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

class LeadController
{
    private const STATUSES = ['nieuw', 'gekwalificeerd', 'offerte', 'gewonnen', 'verloren'];

    public function index(): void
    {
        Permission::require('crm.view');
        $tenantId = (int) Auth::user()['tenant_id'];
        $status = $_GET['status'] ?? '';
        $search = trim($_GET['search'] ?? '');

        $where = ['tenant_id = ?'];
        $params = [$tenantId];
        if (in_array($status, self::STATUSES, true)) {
            $where[] = 'status = ?';
            $params[] = $status;
        }
        if ($search !== '') {
            $where[] = '(name LIKE ? OR company LIKE ? OR email LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $leads = Database::fetchAll(
            "SELECT * FROM crm_leads WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC",
            $params
        );

        View::render('modules/crm/views/leads/index', [
            'leads' => $leads,
            'status' => $status,
            'search' => $search,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(): void
    {
        Permission::require('crm.manage');
        View::render('modules/crm/views/leads/create', ['statuses' => self::STATUSES]);
    }

    public function store(): void
    {
        Permission::require('crm.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        Database::insert('crm_leads', [
            'tenant_id' => $tenantId,
            'name' => trim($_POST['name'] ?? ''),
            'company' => trim($_POST['company'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'source' => trim($_POST['source'] ?? ''),
            'status' => in_array($_POST['status'] ?? '', self::STATUSES, true) ? $_POST['status'] : 'nieuw',
            'value' => (float) ($_POST['value'] ?? 0),
            'notes' => trim($_POST['notes'] ?? ''),
        ]);

        header('Location: ' . BASE . '/crm/leads');
        exit;
    }

    public function show(string $id): void
    {
        Permission::require('crm.view');
        $tenantId = (int) Auth::user()['tenant_id'];

        $lead = Database::fetch("SELECT * FROM crm_leads WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
        if (!$lead) {
            http_response_code(404);
            echo 'Lead niet gevonden';
            return;
        }

        $quotes = Database::fetchAll(
            "SELECT * FROM crm_quotes WHERE lead_id = ? AND tenant_id = ? ORDER BY created_at DESC",
            [(int) $id, $tenantId]
        );

        View::render('modules/crm/views/leads/show', ['lead' => $lead, 'quotes' => $quotes]);
    }

    public function edit(string $id): void
    {
        Permission::require('crm.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $lead = Database::fetch("SELECT * FROM crm_leads WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
        if (!$lead) {
            http_response_code(404);
            echo 'Lead niet gevonden';
            return;
        }

        View::render('modules/crm/views/leads/edit', ['lead' => $lead, 'statuses' => self::STATUSES]);
    }

    public function update(string $id): void
    {
        Permission::require('crm.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        Database::update('crm_leads', [
            'name' => trim($_POST['name'] ?? ''),
            'company' => trim($_POST['company'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'source' => trim($_POST['source'] ?? ''),
            'status' => in_array($_POST['status'] ?? '', self::STATUSES, true) ? $_POST['status'] : 'nieuw',
            'value' => (float) ($_POST['value'] ?? 0),
            'notes' => trim($_POST['notes'] ?? ''),
        ], 'id = ? AND tenant_id = ?', [(int) $id, $tenantId]);

        header('Location: ' . BASE . '/crm/leads/' . $id);
        exit;
    }
}
