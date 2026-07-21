<?php
declare(strict_types=1);

namespace Modules\Facturatie\Controllers;

use Core\Database;
use Core\Auth;
use Core\View;

class InvoiceController
{
    public function index(): void
    {
        $tenantId = Auth::user()['tenant_id'];
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        $where = 'i.tenant_id = ?';
        $params = [$tenantId];

        if ($status && in_array($status, ['concept', 'verstuurd', 'betaald', 'achterstallig', 'geannuleerd'])) {
            $where .= ' AND i.status = ?';
            $params[] = $status;
        }

        if ($search) {
            $where .= ' AND (i.number LIKE ? OR c.name LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $invoices = Database::fetchAll(
            "SELECT i.*, c.name as customer_name FROM fa_invoices i JOIN fa_customers c ON i.customer_id = c.id WHERE $where ORDER BY i.created_at DESC",
            $params
        );

        View::render('modules/facturatie/views/invoices/index', [
            'invoices' => $invoices,
            'currentStatus' => $status,
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $customers = Database::fetchAll(
            "SELECT id, name FROM fa_customers WHERE tenant_id = ? ORDER BY name ASC",
            [$tenantId]
        );

        View::render('modules/facturatie/views/invoices/create', [
            'customers' => $customers,
        ]);
    }

    public function store(): void
    {
        $tenantId = Auth::user()['tenant_id'];
        $customerId = (int) ($_POST['customer_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');
        $dueDate = $_POST['due_date'] ?? date('Y-m-d', strtotime('+30 days'));

        $year = date('Y');
        $lastInvoice = Database::fetch(
            "SELECT number FROM fa_invoices WHERE tenant_id = ? AND number LIKE ? ORDER BY id DESC LIMIT 1",
            [$tenantId, "F-$year-%"]
        );

        if ($lastInvoice) {
            $seq = (int) substr($lastInvoice['number'], strrpos($lastInvoice['number'], '-') + 1) + 1;
        } else {
            $seq = 1;
        }
        $number = sprintf("F-%s-%03d", $year, $seq);

        $descriptions = $_POST['item_description'] ?? [];
        $quantities = $_POST['item_quantity'] ?? [];
        $prices = $_POST['item_price'] ?? [];
        $btwRates = $_POST['item_btw'] ?? [];

        $subtotal = 0;
        $btwAmount = 0;
        $btwRate = 21.00;

        $items = [];
        for ($i = 0; $i < count($descriptions); $i++) {
            $desc = trim($descriptions[$i] ?? '');
            if (!$desc) continue;

            $qty = (float) ($quantities[$i] ?? 1);
            $price = (float) ($prices[$i] ?? 0);
            $rate = (float) ($btwRates[$i] ?? 21);
            $lineTotal = $qty * $price;
            $lineBtw = $lineTotal * ($rate / 100);

            $subtotal += $lineTotal;
            $btwAmount += $lineBtw;
            $btwRate = $rate;

            $items[] = [
                'description' => $desc,
                'quantity' => $qty,
                'unit_price' => $price,
                'btw_rate' => $rate,
                'total' => $lineTotal,
            ];
        }

        $total = $subtotal + $btwAmount;

        $invoiceId = Database::insert('fa_invoices', [
            'tenant_id' => $tenantId,
            'customer_id' => $customerId,
            'number' => $number,
            'status' => 'concept',
            'subtotal' => $subtotal,
            'btw_amount' => $btwAmount,
            'total' => $total,
            'btw_rate' => $btwRate,
            'due_date' => $dueDate,
            'notes' => $notes,
        ]);

        foreach ($items as $item) {
            Database::insert('fa_invoice_items', array_merge($item, ['invoice_id' => $invoiceId]));
        }

        header('Location: ' . BASE . '/facturatie/facturen/' . $invoiceId);
        exit;
    }

    public function show(string $id): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $invoice = Database::fetch(
            "SELECT i.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone, c.address as customer_address, c.city as customer_city, c.postal as customer_postal, c.country as customer_country, c.btw_nr as customer_btw_nr FROM fa_invoices i JOIN fa_customers c ON i.customer_id = c.id WHERE i.id = ? AND i.tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$invoice) {
            http_response_code(404);
            echo 'Factuur niet gevonden';
            return;
        }

        $items = Database::fetchAll(
            "SELECT * FROM fa_invoice_items WHERE invoice_id = ? ORDER BY id ASC",
            [$id]
        );

        View::render('modules/facturatie/views/invoices/show', [
            'invoice' => $invoice,
            'items' => $items,
        ]);
    }

    public function updateStatus(string $id): void
    {
        $tenantId = Auth::user()['tenant_id'];
        $status = $_POST['status'] ?? '';

        if (!in_array($status, ['concept', 'verstuurd', 'betaald', 'achterstallig', 'geannuleerd'])) {
            header('Location: ' . BASE . '/facturatie/facturen/' . $id);
            exit;
        }

        $updates = ['status' => $status];
        if ($status === 'betaald') {
            $updates['paid_at'] = date('Y-m-d H:i:s');
        }

        Database::update('fa_invoices', $updates, 'id = ? AND tenant_id = ?', [$id, $tenantId]);
        header('Location: ' . BASE . '/facturatie/facturen/' . $id);
        exit;
    }

    public function pdf(string $id): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $invoice = Database::fetch(
            "SELECT i.*, c.name as customer_name, c.email as customer_email, c.address as customer_address, c.city as customer_city, c.postal as customer_postal, c.country as customer_country, c.btw_nr as customer_btw_nr FROM fa_invoices i JOIN fa_customers c ON i.customer_id = c.id WHERE i.id = ? AND i.tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$invoice) {
            http_response_code(404);
            echo 'Factuur niet gevonden';
            return;
        }

        $items = Database::fetchAll(
            "SELECT * FROM fa_invoice_items WHERE invoice_id = ? ORDER BY id ASC",
            [$id]
        );

        $tenant = Database::fetch("SELECT * FROM tenants WHERE id = ?", [$tenantId]);
        $company = ['name' => $tenant['name'] ?? 'Bedrijf'];

        $pdfContent = \Core\PdfGenerator::invoice($invoice, $items, $company);

        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename=\"{$invoice['number']}.pdf\"");
        header('Content-Length: ' . strlen($pdfContent));
        echo $pdfContent;
        exit;
    }

    public function sendReminder(string $id): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $invoice = Database::fetch(
            "SELECT i.*, c.name as customer_name, c.email as customer_email FROM fa_invoices i JOIN fa_customers c ON i.customer_id = c.id WHERE i.id = ? AND i.tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$invoice || !$invoice['customer_email']) {
            header('Location: ' . BASE . '/facturatie/facturen/' . $id);
            exit;
        }

        $daysOverdue = 0;
        if ($invoice['due_date']) {
            $due = new \DateTime($invoice['due_date']);
            $now = new \DateTime();
            if ($now > $due) {
                $daysOverdue = (int) $now->diff($due)->days;
            }
        }

        $tenant = Database::fetch("SELECT * FROM tenants WHERE id = ?", [$tenantId]);
        $company = ['name' => $tenant['name'] ?? 'Bedrijf'];

        $customer = ['name' => $invoice['customer_name'], 'email' => $invoice['customer_email']];

        \Core\Email::invoiceReminder($invoice, $customer, $company, $daysOverdue);

        Database::insert('fa_reminders', [
            'invoice_id' => $id,
            'type' => 'eerste',
        ]);

        header('Location: ' . BASE . '/facturatie/facturen/' . $id);
        exit;
    }
}
