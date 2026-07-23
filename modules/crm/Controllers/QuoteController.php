<?php
declare(strict_types=1);

namespace Modules\Crm\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

class QuoteController
{
    private const STATUSES = ['concept', 'verstuurd', 'geaccepteerd', 'afgewezen'];

    public function index(): void
    {
        Permission::require('crm.view');
        $tenantId = (int) Auth::user()['tenant_id'];

        $quotes = Database::fetchAll(
            "SELECT q.*, l.name AS lead_name
             FROM crm_quotes q
             LEFT JOIN crm_leads l ON q.lead_id = l.id
             WHERE q.tenant_id = ?
             ORDER BY q.created_at DESC",
            [$tenantId]
        );

        View::render('modules/crm/views/quotes/index', ['quotes' => $quotes, 'statuses' => self::STATUSES]);
    }

    public function create(): void
    {
        Permission::require('crm.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $leads = Database::fetchAll(
            "SELECT id, name, company FROM crm_leads WHERE tenant_id = ? ORDER BY name",
            [$tenantId]
        );

        View::render('modules/crm/views/quotes/create', ['leads' => $leads]);
    }

    public function store(): void
    {
        Permission::require('crm.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $number = 'OFF-' . date('Y') . '-' . str_pad((string) (Database::count('crm_quotes', 'tenant_id = ?', [$tenantId]) + 1), 4, '0', STR_PAD_LEFT);
        $leadId = (int) ($_POST['lead_id'] ?? 0);

        // Beveiliging: lead_id moet bij deze tenant horen (zie InvoiceController::
        // store() voor dezelfde reden — anders lekt een andere tenant's lead-naam
        // mee via de LEFT JOIN in QuoteController::index()/show()).
        if ($leadId > 0 && !Database::fetch("SELECT id FROM crm_leads WHERE id = ? AND tenant_id = ?", [$leadId, $tenantId])) {
            $leadId = 0;
        }

        $id = Database::insert('crm_quotes', [
            'tenant_id' => $tenantId,
            'lead_id' => $leadId > 0 ? $leadId : null,
            'number' => $number,
            'title' => trim($_POST['title'] ?? ''),
            'status' => 'concept',
            'amount' => (float) ($_POST['amount'] ?? 0),
            'valid_until' => $_POST['valid_until'] ?: null,
            'notes' => trim($_POST['notes'] ?? ''),
        ]);

        // Een lead die een offerte krijgt, schuift automatisch door in de pijplijn.
        if ($leadId > 0) {
            Database::update('crm_leads', ['status' => 'offerte'], 'id = ? AND tenant_id = ?', [$leadId, $tenantId]);
        }

        header('Location: ' . BASE . '/crm/offertes');
        exit;
    }

    public function updateStatus(string $id): void
    {
        Permission::require('crm.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        $status = $_POST['status'] ?? '';

        if (!in_array($status, self::STATUSES, true)) {
            header('Location: ' . BASE . '/crm/offertes');
            exit;
        }

        Database::update('crm_quotes', ['status' => $status], 'id = ? AND tenant_id = ?', [(int) $id, $tenantId]);

        if ($status === 'geaccepteerd') {
            $quote = Database::fetch("SELECT * FROM crm_quotes WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
            if ($quote && $quote['lead_id']) {
                Database::update('crm_leads', ['status' => 'gewonnen'], 'id = ? AND tenant_id = ?', [$quote['lead_id'], $tenantId]);
            }
        }

        header('Location: ' . BASE . '/crm/offertes');
        exit;
    }
}
