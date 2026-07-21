<?php
declare(strict_types=1);

namespace Modules\Voorraad\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

class ProductController
{
    public function index(): void
    {
        Permission::require('voorraad.view');
        $tenantId = (int) Auth::user()['tenant_id'];
        $search = trim($_GET['search'] ?? '');

        $where = ['p.tenant_id = ?'];
        $params = [$tenantId];
        if ($search !== '') {
            $where[] = '(p.name LIKE ? OR p.sku LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $products = Database::fetchAll(
            "SELECT p.*, COALESCE(SUM(s.quantity), 0) AS total_qty
             FROM voorraad_products p
             LEFT JOIN voorraad_stock s ON s.product_id = p.id
             WHERE " . implode(' AND ', $where) . "
             GROUP BY p.id
             ORDER BY p.name ASC",
            $params
        );

        View::render('modules/voorraad/views/products/index', ['products' => $products, 'search' => $search]);
    }

    public function create(): void
    {
        Permission::require('voorraad.manage');
        View::render('modules/voorraad/views/products/create', []);
    }

    public function store(): void
    {
        Permission::require('voorraad.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $id = Database::insert('voorraad_products', [
            'tenant_id' => $tenantId,
            'sku' => trim($_POST['sku'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'unit' => trim($_POST['unit'] ?? '') ?: 'stuks',
            'purchase_price' => (float) ($_POST['purchase_price'] ?? 0),
            'sales_price' => (float) ($_POST['sales_price'] ?? 0),
            'min_stock' => (int) ($_POST['min_stock'] ?? 0),
        ]);

        header('Location: ' . BASE . '/voorraad/producten/' . $id);
        exit;
    }

    public function show(string $id): void
    {
        Permission::require('voorraad.view');
        $tenantId = (int) Auth::user()['tenant_id'];

        $product = Database::fetch("SELECT * FROM voorraad_products WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
        if (!$product) {
            http_response_code(404);
            echo 'Product niet gevonden';
            return;
        }

        $stockPerWarehouse = Database::fetchAll(
            "SELECT w.id AS warehouse_id, w.name AS warehouse_name, COALESCE(s.quantity, 0) AS quantity
             FROM voorraad_warehouses w
             LEFT JOIN voorraad_stock s ON s.warehouse_id = w.id AND s.product_id = ?
             WHERE w.tenant_id = ?
             ORDER BY w.name",
            [(int) $id, $tenantId]
        );

        View::render('modules/voorraad/views/products/show', [
            'product' => $product,
            'stockPerWarehouse' => $stockPerWarehouse,
        ]);
    }

    public function edit(string $id): void
    {
        Permission::require('voorraad.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $product = Database::fetch("SELECT * FROM voorraad_products WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
        if (!$product) {
            http_response_code(404);
            echo 'Product niet gevonden';
            return;
        }

        View::render('modules/voorraad/views/products/edit', ['product' => $product]);
    }

    public function update(string $id): void
    {
        Permission::require('voorraad.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        Database::update('voorraad_products', [
            'sku' => trim($_POST['sku'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'unit' => trim($_POST['unit'] ?? '') ?: 'stuks',
            'purchase_price' => (float) ($_POST['purchase_price'] ?? 0),
            'sales_price' => (float) ($_POST['sales_price'] ?? 0),
            'min_stock' => (int) ($_POST['min_stock'] ?? 0),
        ], 'id = ? AND tenant_id = ?', [(int) $id, $tenantId]);

        header('Location: ' . BASE . '/voorraad/producten/' . $id);
        exit;
    }

    /**
     * Voorraadmutatie voor product {id} in een magazijn. Gebruikt een
     * transactie met SELECT ... FOR UPDATE zodat gelijktijdige mutaties op
     * dezelfde product/magazijn-combinatie niet elkaars wijziging overschrijven.
     */
    public function adjustStock(string $id): void
    {
        Permission::require('voorraad.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        $warehouseId = (int) ($_POST['warehouse_id'] ?? 0);
        $delta = (int) ($_POST['delta'] ?? 0);

        $product = Database::fetch("SELECT id FROM voorraad_products WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
        $warehouse = Database::fetch("SELECT id FROM voorraad_warehouses WHERE id = ? AND tenant_id = ?", [$warehouseId, $tenantId]);

        if (!$product || !$warehouse || $delta === 0) {
            header('Location: ' . BASE . '/voorraad/producten/' . $id);
            exit;
        }

        $pdo = Database::connect();
        $pdo->beginTransaction();
        try {
            $row = $pdo->prepare(
                "SELECT id, quantity FROM voorraad_stock WHERE product_id = ? AND warehouse_id = ? FOR UPDATE"
            );
            $row->execute([(int) $id, $warehouseId]);
            $stock = $row->fetch();

            if ($stock) {
                $newQty = max(0, (int) $stock['quantity'] + $delta);
                $upd = $pdo->prepare("UPDATE voorraad_stock SET quantity = ? WHERE id = ?");
                $upd->execute([$newQty, $stock['id']]);
            } else {
                $newQty = max(0, $delta);
                $ins = $pdo->prepare(
                    "INSERT INTO voorraad_stock (tenant_id, product_id, warehouse_id, quantity) VALUES (?, ?, ?, ?)"
                );
                $ins->execute([$tenantId, (int) $id, $warehouseId, $newQty]);
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
        }

        header('Location: ' . BASE . '/voorraad/producten/' . $id);
        exit;
    }
}
