<?php
declare(strict_types=1);

namespace Api\V1\Controllers;

use Core\Database;

/**
 * GET /api/v1/facturen — facturen van de tenant die bij het API-token hoort
 * (partnerintegratie, bv. een boekhoudkoppeling). Tenant-scoping komt hier
 * volledig uit het geverifieerde token (public/api/index.php), niet uit de
 * sessie — er ís geen sessie bij API-requests.
 */
class FacturenController
{
    public function __construct(private int $tenantId)
    {
    }

    public function index(): void
    {
        if (!Database::tableExists('fa_invoices')) {
            $this->json(200, ['data' => []]);
            return;
        }

        $status = $_GET['status'] ?? null;
        $where = ['i.tenant_id = ?'];
        $params = [$this->tenantId];
        if ($status !== null && $status !== '') {
            $where[] = 'i.status = ?';
            $params[] = $status;
        }

        $rows = Database::fetchAll(
            "SELECT i.id, i.number, i.status, i.subtotal, i.btw_amount, i.total, i.due_date, i.paid_at, i.created_at,
                    c.name AS customer_name
             FROM fa_invoices i
             JOIN fa_customers c ON i.customer_id = c.id
             WHERE " . implode(' AND ', $where) . "
             ORDER BY i.created_at DESC
             LIMIT 200",
            $params
        );

        $this->json(200, ['data' => $rows]);
    }

    public function show(string $id): void
    {
        if (!Database::tableExists('fa_invoices')) {
            $this->json(404, ['error' => 'Factuur niet gevonden']);
            return;
        }

        $invoice = Database::fetch(
            "SELECT i.*, c.name AS customer_name
             FROM fa_invoices i
             JOIN fa_customers c ON i.customer_id = c.id
             WHERE i.id = ? AND i.tenant_id = ?",
            [(int) $id, $this->tenantId]
        );

        if (!$invoice) {
            $this->json(404, ['error' => 'Factuur niet gevonden']);
            return;
        }

        $items = Database::fetchAll("SELECT * FROM fa_invoice_items WHERE invoice_id = ?", [(int) $id]);
        $invoice['items'] = $items;

        $this->json(200, ['data' => $invoice]);
    }

    private function json(int $status, array $payload): void
    {
        http_response_code($status);
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
