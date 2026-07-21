<?php
declare(strict_types=1);

namespace Modules\Dashboard\Controllers;

use Core\Auth;
use Core\Database;
use Core\Tenant;
use Core\View;

/**
 * Kernmodule-dashboard: waardegedreven KPI-widgets (omzet, openstaande
 * facturen, projectstatus, klantengroei) op basis van bestaande fa_, hr_ en
 * projecten_ tabellen, plus de module-kaarten ("App Store"-overzicht) voor
 * alle actieve modules van de tenant.
 *
 * Belangrijk: de fa_, hr_ en ct_ tabellen worden (anders dan de SaaS-kerntabellen)
 * niet automatisch aangemaakt door Database::initSchema() — die verwachten een
 * handmatige import van config/schema.sql. Elke query hieronder is daarom
 * defensief achter Database::tableExists() gezet, zodat een tenant die net is
 * aangemaakt (of een omgeving waar schema.sql nog niet is geïmporteerd) geen
 * fatal error op het dashboard krijgt.
 */
class DashboardController
{
    public function index(): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];
        $modules = Tenant::activeModules();

        $revenue = $this->revenueThisMonth($tenantId);
        $outstanding = $this->outstandingInvoices($tenantId);
        $projectStatus = $this->projectStatus($tenantId);
        $customerGrowth = $this->customerGrowth($tenantId);

        View::render('modules/dashboard/views/index', [
            'modules' => $modules,
            'revenue' => $revenue,
            'outstanding' => $outstanding,
            'projectStatus' => $projectStatus,
            'customerGrowth' => $customerGrowth,
        ]);
    }

    /** Omzet deze maand (betaalde facturen), alleen als facturatie actief + tabel bestaat. */
    private function revenueThisMonth(int $tenantId): ?array
    {
        if (!Tenant::hasModule('facturatie') || !Database::tableExists('fa_invoices')) {
            return null;
        }
        $row = Database::fetch(
            "SELECT COALESCE(SUM(total), 0) AS amount, COUNT(*) AS count
             FROM fa_invoices
             WHERE tenant_id = ? AND status = 'betaald' AND MONTH(paid_at) = MONTH(NOW()) AND YEAR(paid_at) = YEAR(NOW())",
            [$tenantId]
        );
        $total = Database::fetch(
            "SELECT COALESCE(SUM(total), 0) AS amount FROM fa_invoices WHERE tenant_id = ? AND status = 'betaald'",
            [$tenantId]
        );
        return [
            'month_amount' => (float) ($row['amount'] ?? 0),
            'month_count' => (int) ($row['count'] ?? 0),
            'total_amount' => (float) ($total['amount'] ?? 0),
        ];
    }

    /** Openstaande facturen (verstuurd/achterstallig). */
    private function outstandingInvoices(int $tenantId): ?array
    {
        if (!Tenant::hasModule('facturatie') || !Database::tableExists('fa_invoices')) {
            return null;
        }
        $row = Database::fetch(
            "SELECT COALESCE(SUM(total), 0) AS amount, COUNT(*) AS count
             FROM fa_invoices WHERE tenant_id = ? AND status IN ('verstuurd','achterstallig')",
            [$tenantId]
        );
        $overdue = Database::count('fa_invoices', "tenant_id = ? AND status = 'achterstallig'", [$tenantId]);
        return [
            'amount' => (float) ($row['amount'] ?? 0),
            'count' => (int) ($row['count'] ?? 0),
            'overdue_count' => $overdue,
        ];
    }

    /** Projectstatus-verdeling, alleen als projecten actief + tabel bestaat. */
    private function projectStatus(int $tenantId): ?array
    {
        if (!Tenant::hasModule('projecten') || !Database::tableExists('projecten_projects')) {
            return null;
        }
        $rows = Database::fetchAll(
            "SELECT status, COUNT(*) AS c FROM projecten_projects WHERE tenant_id = ? GROUP BY status",
            [$tenantId]
        );
        $counts = ['gepland' => 0, 'actief' => 0, 'on_hold' => 0, 'afgerond' => 0, 'geannuleerd' => 0];
        foreach ($rows as $r) {
            $counts[$r['status']] = (int) $r['c'];
        }
        $counts['totaal'] = array_sum($counts);
        return $counts;
    }

    /** Klantengroei: nieuwe klanten deze maand vs. vorige maand (facturatie fa_customers, anders crm_leads). */
    private function customerGrowth(int $tenantId): ?array
    {
        $table = null;
        if (Tenant::hasModule('facturatie') && Database::tableExists('fa_customers')) {
            $table = 'fa_customers';
        } elseif (Tenant::hasModule('crm') && Database::tableExists('crm_leads')) {
            $table = 'crm_leads';
        }
        if ($table === null) {
            return null;
        }

        $thisMonth = Database::count(
            $table,
            'tenant_id = ? AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())',
            [$tenantId]
        );
        $lastMonth = Database::count(
            $table,
            "tenant_id = ? AND MONTH(created_at) = MONTH(NOW() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(NOW() - INTERVAL 1 MONTH)",
            [$tenantId]
        );
        $total = Database::count($table, 'tenant_id = ?', [$tenantId]);
        $growthPct = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100)
            : ($thisMonth > 0 ? 100 : 0);

        return [
            'label' => $table === 'crm_leads' ? 'leads' : 'klanten',
            'total' => $total,
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'growth_pct' => $growthPct,
        ];
    }
}
