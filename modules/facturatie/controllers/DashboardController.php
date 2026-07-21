<?php
declare(strict_types=1);

namespace Modules\Facturatie\Controllers;

use Core\Database;
use Core\Auth;
use Core\View;

class DashboardController
{
    public function index(): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $totalCustomers = Database::count('fa_customers', 'tenant_id = ?', [$tenantId]);

        $totalInvoices = Database::count('fa_invoices', 'tenant_id = ?', [$tenantId]);

        $openAmount = Database::fetch(
            "SELECT COALESCE(SUM(total), 0) as amount FROM fa_invoices WHERE tenant_id = ? AND status IN ('verstuurd','achterstallig')",
            [$tenantId]
        )['amount'];

        $paidThisMonth = Database::fetch(
            "SELECT COALESCE(SUM(total), 0) as amount FROM fa_invoices WHERE tenant_id = ? AND status = 'betaald' AND MONTH(paid_at) = MONTH(NOW()) AND YEAR(paid_at) = YEAR(NOW())",
            [$tenantId]
        )['amount'];

        $totalRevenue = Database::fetch(
            "SELECT COALESCE(SUM(total), 0) as amount FROM fa_invoices WHERE tenant_id = ? AND status = 'betaald'",
            [$tenantId]
        )['amount'];

        $recentInvoices = Database::fetchAll(
            "SELECT i.*, c.name as customer_name FROM fa_invoices i JOIN fa_customers c ON i.customer_id = c.id WHERE i.tenant_id = ? ORDER BY i.created_at DESC LIMIT 10",
            [$tenantId]
        );

        View::render('modules/facturatie/views/dashboard/index', [
            'totalCustomers' => $totalCustomers,
            'totalInvoices' => $totalInvoices,
            'openAmount' => $openAmount,
            'paidThisMonth' => $paidThisMonth,
            'totalRevenue' => $totalRevenue,
            'recentInvoices' => $recentInvoices,
        ]);
    }
}
