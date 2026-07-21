<?php
declare(strict_types=1);

namespace Modules\Facturatie\Controllers;

use Core\Database;
use Core\Auth;
use Core\View;

class CustomerController
{
    public function index(): void
    {
        $tenantId = Auth::user()['tenant_id'];
        $search = $_GET['search'] ?? '';

        if ($search) {
            $customers = Database::fetchAll(
                "SELECT * FROM fa_customers WHERE tenant_id = ? AND (name LIKE ? OR email LIKE ? OR btw_nr LIKE ?) ORDER BY name ASC",
                [$tenantId, "%$search%", "%$search%", "%$search%"]
            );
        } else {
            $customers = Database::fetchAll(
                "SELECT * FROM fa_customers WHERE tenant_id = ? ORDER BY name ASC",
                [$tenantId]
            );
        }

        View::render('modules/facturatie/views/customers/index', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        View::render('modules/facturatie/views/customers/create');
    }

    public function store(): void
    {
        $tenantId = Auth::user()['tenant_id'];

        Database::insert('fa_customers', [
            'tenant_id' => $tenantId,
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'postal' => trim($_POST['postal'] ?? ''),
            'country' => trim($_POST['country'] ?? 'Nederland'),
            'btw_nr' => trim($_POST['btw_nr'] ?? ''),
            'kvk_nr' => trim($_POST['kvk_nr'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
        ]);

        header('Location: ' . BASE . '/facturatie/klanten');
        exit;
    }

    public function show(string $id): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $customer = Database::fetch(
            "SELECT * FROM fa_customers WHERE id = ? AND tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$customer) {
            http_response_code(404);
            echo 'Klant niet gevonden';
            return;
        }

        $invoices = Database::fetchAll(
            "SELECT * FROM fa_invoices WHERE customer_id = ? AND tenant_id = ? ORDER BY created_at DESC",
            [$id, $tenantId]
        );

        View::render('modules/facturatie/views/customers/show', [
            'customer' => $customer,
            'invoices' => $invoices,
        ]);
    }

    public function edit(string $id): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $customer = Database::fetch(
            "SELECT * FROM fa_customers WHERE id = ? AND tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$customer) {
            http_response_code(404);
            echo 'Klant niet gevonden';
            return;
        }

        View::render('modules/facturatie/views/customers/edit', [
            'customer' => $customer,
        ]);
    }

    public function update(string $id): void
    {
        $tenantId = Auth::user()['tenant_id'];

        Database::update('fa_customers', [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'postal' => trim($_POST['postal'] ?? ''),
            'country' => trim($_POST['country'] ?? 'Nederland'),
            'btw_nr' => trim($_POST['btw_nr'] ?? ''),
            'kvk_nr' => trim($_POST['kvk_nr'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
        ], 'id = ? AND tenant_id = ?', [$id, $tenantId]);

        header('Location: ' . BASE . '/facturatie/klanten/$id");
        exit;
    }

    public function delete(string $id): void
    {
        $tenantId = Auth::user()['tenant_id'];
        Database::delete('fa_customers', 'id = ? AND tenant_id = ?', [$id, $tenantId]);
        header('Location: ' . BASE . '/facturatie/klanten');
        exit;
    }
}
