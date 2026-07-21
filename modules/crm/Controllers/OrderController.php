<?php
declare(strict_types=1);

namespace Modules\Crm\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

class OrderController
{
    private const STATUSES = ['nieuw', 'in_behandeling', 'geleverd', 'gefactureerd', 'geannuleerd'];

    public function index(): void
    {
        Permission::require('crm.view');
        $tenantId = (int) Auth::user()['tenant_id'];

        $orders = Database::fetchAll(
            "SELECT o.*, q.number AS quote_number
             FROM crm_orders o
             LEFT JOIN crm_quotes q ON o.quote_id = q.id
             WHERE o.tenant_id = ?
             ORDER BY o.created_at DESC",
            [$tenantId]
        );

        View::render('modules/crm/views/orders/index', ['orders' => $orders, 'statuses' => self::STATUSES]);
    }

    public function create(): void
    {
        Permission::require('crm.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $quotes = Database::fetchAll(
            "SELECT id, number, title, amount FROM crm_quotes WHERE tenant_id = ? AND status = 'geaccepteerd' ORDER BY created_at DESC",
            [$tenantId]
        );

        View::render('modules/crm/views/orders/create', ['quotes' => $quotes]);
    }

    public function store(): void
    {
        Permission::require('crm.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $number = 'ORD-' . date('Y') . '-' . str_pad((string) (Database::count('crm_orders', 'tenant_id = ?', [$tenantId]) + 1), 4, '0', STR_PAD_LEFT);
        $quoteId = (int) ($_POST['quote_id'] ?? 0);

        Database::insert('crm_orders', [
            'tenant_id' => $tenantId,
            'quote_id' => $quoteId > 0 ? $quoteId : null,
            'number' => $number,
            'customer_name' => trim($_POST['customer_name'] ?? ''),
            'status' => 'nieuw',
            'amount' => (float) ($_POST['amount'] ?? 0),
            'order_date' => $_POST['order_date'] ?: date('Y-m-d'),
            'notes' => trim($_POST['notes'] ?? ''),
        ]);

        header('Location: ' . BASE . '/crm/orders');
        exit;
    }

    public function updateStatus(string $id): void
    {
        Permission::require('crm.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        $status = $_POST['status'] ?? '';

        if (!in_array($status, self::STATUSES, true)) {
            header('Location: ' . BASE . '/crm/orders');
            exit;
        }

        Database::update('crm_orders', ['status' => $status], 'id = ? AND tenant_id = ?', [(int) $id, $tenantId]);
        header('Location: ' . BASE . '/crm/orders');
        exit;
    }
}
