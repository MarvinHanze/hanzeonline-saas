<?php
declare(strict_types=1);

namespace Modules\Voorraad\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

class DashboardController
{
    public function index(): void
    {
        Permission::require('voorraad.view');
        $tenantId = (int) Auth::user()['tenant_id'];

        $totalProducts = Database::count('voorraad_products', 'tenant_id = ?', [$tenantId]);

        $lowStock = Database::fetchAll(
            "SELECT p.id, p.name, p.sku, p.min_stock, COALESCE(SUM(s.quantity), 0) AS total_qty
             FROM voorraad_products p
             LEFT JOIN voorraad_stock s ON s.product_id = p.id
             WHERE p.tenant_id = ?
             GROUP BY p.id, p.name, p.sku, p.min_stock
             HAVING total_qty <= p.min_stock
             ORDER BY total_qty ASC
             LIMIT 10",
            [$tenantId]
        );

        $openPurchaseOrders = Database::count(
            'voorraad_purchase_orders',
            "tenant_id = ? AND status IN ('concept','besteld')",
            [$tenantId]
        );

        $equipmentInUse = Database::count('voorraad_equipment', "tenant_id = ? AND status = 'in_gebruik'", [$tenantId]);
        $equipmentTotal = Database::count('voorraad_equipment', 'tenant_id = ?', [$tenantId]);

        View::render('modules/voorraad/views/dashboard/index', [
            'totalProducts' => $totalProducts,
            'lowStock' => $lowStock,
            'openPurchaseOrders' => $openPurchaseOrders,
            'equipmentInUse' => $equipmentInUse,
            'equipmentTotal' => $equipmentTotal,
        ]);
    }
}
