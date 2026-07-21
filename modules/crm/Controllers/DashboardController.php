<?php
declare(strict_types=1);

namespace Modules\Crm\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

class DashboardController
{
    public function index(): void
    {
        Permission::require('crm.view');
        $tenantId = (int) Auth::user()['tenant_id'];

        $totalLeads = Database::count('crm_leads', 'tenant_id = ?', [$tenantId]);
        $pipelineValue = Database::fetch(
            "SELECT COALESCE(SUM(value), 0) AS amount FROM crm_leads WHERE tenant_id = ? AND status NOT IN ('gewonnen','verloren')",
            [$tenantId]
        )['amount'];
        $quotesSent = Database::count('crm_quotes', "tenant_id = ? AND status IN ('verstuurd','geaccepteerd')", [$tenantId]);
        $ordersWonAmount = Database::fetch(
            "SELECT COALESCE(SUM(amount), 0) AS amount FROM crm_orders WHERE tenant_id = ? AND status != 'geannuleerd'",
            [$tenantId]
        )['amount'];

        $recentLeads = Database::fetchAll(
            "SELECT * FROM crm_leads WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 8",
            [$tenantId]
        );

        View::render('modules/crm/views/dashboard/index', [
            'totalLeads' => $totalLeads,
            'pipelineValue' => (float) $pipelineValue,
            'quotesSent' => $quotesSent,
            'ordersWonAmount' => (float) $ordersWonAmount,
            'recentLeads' => $recentLeads,
        ]);
    }
}
