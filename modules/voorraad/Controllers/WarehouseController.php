<?php
declare(strict_types=1);

namespace Modules\Voorraad\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

class WarehouseController
{
    public function index(): void
    {
        Permission::require('voorraad.view');
        $tenantId = (int) Auth::user()['tenant_id'];

        $warehouses = Database::fetchAll(
            "SELECT w.*, COALESCE(SUM(s.quantity), 0) AS total_qty
             FROM voorraad_warehouses w
             LEFT JOIN voorraad_stock s ON s.warehouse_id = w.id
             WHERE w.tenant_id = ?
             GROUP BY w.id
             ORDER BY w.name",
            [$tenantId]
        );

        View::render('modules/voorraad/views/warehouses/index', ['warehouses' => $warehouses]);
    }

    public function store(): void
    {
        Permission::require('voorraad.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        Database::insert('voorraad_warehouses', [
            'tenant_id' => $tenantId,
            'name' => trim($_POST['name'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
        ]);

        header('Location: ' . BASE . '/voorraad/magazijnen');
        exit;
    }
}
