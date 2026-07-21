<?php
declare(strict_types=1);

namespace Modules\Contract\Controllers;

use Core\Database;
use Core\Auth;
use Core\View;

class DashboardController
{
    public function index(): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $totalActive = Database::count('ct_contracts', 'tenant_id = ? AND status = "actief"', [$tenantId]);
        $expired = Database::count('ct_contracts', 'tenant_id = ? AND status = "verlopen"', [$tenantId]);
        $pendingSign = Database::count('ct_contracts', 'tenant_id = ? AND status = "concept" AND signed_at IS NULL', [$tenantId]);
        $totalTemplates = Database::count('ct_templates', 'tenant_id = ?', [$tenantId]);

        $expiringSoon = Database::fetchAll(
            "SELECT c.*, e.name as employee_name, cu.name as customer_name
             FROM ct_contracts c
             LEFT JOIN hr_employees e ON c.employee_id = e.id
             LEFT JOIN fa_customers cu ON c.customer_id = cu.id
             WHERE c.tenant_id = ? AND c.status = 'actief'
             AND c.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
             ORDER BY c.end_date ASC LIMIT 10",
            [$tenantId]
        );

        View::render('modules/contract/views/dashboard/index', [
            'totalActive' => $totalActive,
            'expired' => $expired,
            'pendingSign' => $pendingSign,
            'totalTemplates' => $totalTemplates,
            'expiringSoon' => $expiringSoon,
        ]);
    }
}
