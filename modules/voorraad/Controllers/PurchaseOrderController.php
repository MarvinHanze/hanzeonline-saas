<?php
declare(strict_types=1);

namespace Modules\Voorraad\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

class PurchaseOrderController
{
    private const STATUSES = ['concept', 'besteld', 'ontvangen', 'geannuleerd'];

    public function index(): void
    {
        Permission::require('voorraad.view');
        $tenantId = (int) Auth::user()['tenant_id'];

        $orders = Database::fetchAll(
            "SELECT po.*, w.name AS warehouse_name
             FROM voorraad_purchase_orders po
             LEFT JOIN voorraad_warehouses w ON po.warehouse_id = w.id
             WHERE po.tenant_id = ?
             ORDER BY po.created_at DESC",
            [$tenantId]
        );

        View::render('modules/voorraad/views/purchase_orders/index', ['orders' => $orders, 'statuses' => self::STATUSES]);
    }

    public function create(): void
    {
        Permission::require('voorraad.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $products = Database::fetchAll("SELECT id, sku, name, purchase_price FROM voorraad_products WHERE tenant_id = ? ORDER BY name", [$tenantId]);
        $warehouses = Database::fetchAll("SELECT id, name FROM voorraad_warehouses WHERE tenant_id = ? ORDER BY name", [$tenantId]);

        View::render('modules/voorraad/views/purchase_orders/create', ['products' => $products, 'warehouses' => $warehouses]);
    }

    public function store(): void
    {
        Permission::require('voorraad.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        $unitPrice = (float) ($_POST['unit_price'] ?? 0);
        $total = $quantity * $unitPrice;
        $warehouseId = (int) ($_POST['warehouse_id'] ?? 0);

        // Beveiliging: product_id/warehouse_id moeten bij DEZE tenant horen —
        // anders zou een gemanipuleerd id een inkooporderregel kunnen koppelen aan
        // een product/magazijn van een andere tenant.
        if ($productId > 0 && !Database::fetch("SELECT id FROM voorraad_products WHERE id = ? AND tenant_id = ?", [$productId, $tenantId])) {
            $productId = 0;
        }
        if ($warehouseId > 0 && !Database::fetch("SELECT id FROM voorraad_warehouses WHERE id = ? AND tenant_id = ?", [$warehouseId, $tenantId])) {
            $warehouseId = 0;
        }

        $number = 'PO-' . date('Y') . '-' . str_pad((string) (Database::count('voorraad_purchase_orders', 'tenant_id = ?', [$tenantId]) + 1), 4, '0', STR_PAD_LEFT);

        $orderId = Database::insert('voorraad_purchase_orders', [
            'tenant_id' => $tenantId,
            'number' => $number,
            'supplier_name' => trim($_POST['supplier_name'] ?? ''),
            'status' => 'concept',
            'order_date' => $_POST['order_date'] ?: date('Y-m-d'),
            'expected_date' => $_POST['expected_date'] ?: null,
            'warehouse_id' => $warehouseId > 0 ? $warehouseId : null,
            'total' => $total,
            'notes' => trim($_POST['notes'] ?? ''),
        ]);

        if ($productId > 0) {
            Database::insert('voorraad_purchase_order_items', [
                'purchase_order_id' => $orderId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $total,
            ]);
        }

        header('Location: ' . BASE . '/voorraad/inkooporders/' . $orderId);
        exit;
    }

    public function show(string $id): void
    {
        Permission::require('voorraad.view');
        $tenantId = (int) Auth::user()['tenant_id'];

        $order = Database::fetch("SELECT * FROM voorraad_purchase_orders WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
        if (!$order) {
            http_response_code(404);
            echo 'Inkooporder niet gevonden';
            return;
        }

        $items = Database::fetchAll(
            "SELECT poi.*, p.name AS product_name, p.sku
             FROM voorraad_purchase_order_items poi
             JOIN voorraad_products p ON poi.product_id = p.id
             WHERE poi.purchase_order_id = ?",
            [(int) $id]
        );

        View::render('modules/voorraad/views/purchase_orders/show', [
            'order' => $order,
            'items' => $items,
            'statuses' => self::STATUSES,
        ]);
    }

    /**
     * Statuswijziging. Bij overgang naar 'ontvangen' wordt de voorraad
     * atomisch (transactie + FOR UPDATE) opgehoogd met de bestelde aantallen —
     * en alleen bij die ene overgang, zodat dubbel ontvangen niet dubbel telt.
     */
    public function updateStatus(string $id): void
    {
        Permission::require('voorraad.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        $status = $_POST['status'] ?? '';

        $order = Database::fetch("SELECT * FROM voorraad_purchase_orders WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
        if (!$order || !in_array($status, self::STATUSES, true)) {
            header('Location: ' . BASE . '/voorraad/inkooporders');
            exit;
        }

        $becomesReceived = $status === 'ontvangen' && $order['status'] !== 'ontvangen';

        if ($becomesReceived && $order['warehouse_id']) {
            $items = Database::fetchAll(
                "SELECT product_id, quantity FROM voorraad_purchase_order_items WHERE purchase_order_id = ?",
                [(int) $id]
            );

            $pdo = Database::connect();
            $pdo->beginTransaction();
            try {
                foreach ($items as $item) {
                    $row = $pdo->prepare("SELECT id, quantity FROM voorraad_stock WHERE product_id = ? AND warehouse_id = ? FOR UPDATE");
                    $row->execute([(int) $item['product_id'], (int) $order['warehouse_id']]);
                    $stock = $row->fetch();

                    if ($stock) {
                        $pdo->prepare("UPDATE voorraad_stock SET quantity = quantity + ? WHERE id = ?")
                            ->execute([(int) $item['quantity'], $stock['id']]);
                    } else {
                        $pdo->prepare(
                            "INSERT INTO voorraad_stock (tenant_id, product_id, warehouse_id, quantity) VALUES (?, ?, ?, ?)"
                        )->execute([$tenantId, (int) $item['product_id'], (int) $order['warehouse_id'], (int) $item['quantity']]);
                    }
                }
                $pdo->prepare("UPDATE voorraad_purchase_orders SET status = ? WHERE id = ? AND tenant_id = ?")
                    ->execute(['ontvangen', (int) $id, $tenantId]);
                $pdo->commit();
            } catch (\Throwable $e) {
                $pdo->rollBack();
            }
        } else {
            Database::update('voorraad_purchase_orders', ['status' => $status], 'id = ? AND tenant_id = ?', [(int) $id, $tenantId]);
        }

        header('Location: ' . BASE . '/voorraad/inkooporders/' . $id);
        exit;
    }
}
