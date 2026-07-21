<?php
declare(strict_types=1);

namespace Api\V1\Controllers;

use Core\Database;

/** GET /api/v1/klanten — klanten (fa_customers) van de tenant achter het API-token. */
class KlantenController
{
    public function __construct(private int $tenantId)
    {
    }

    public function index(): void
    {
        if (!Database::tableExists('fa_customers')) {
            $this->json(200, ['data' => []]);
            return;
        }

        $rows = Database::fetchAll(
            "SELECT id, name, email, phone, city, postal, country, btw_nr, kvk_nr, created_at
             FROM fa_customers WHERE tenant_id = ? ORDER BY name ASC LIMIT 500",
            [$this->tenantId]
        );

        $this->json(200, ['data' => $rows]);
    }

    public function show(string $id): void
    {
        if (!Database::tableExists('fa_customers')) {
            $this->json(404, ['error' => 'Klant niet gevonden']);
            return;
        }

        $customer = Database::fetch(
            "SELECT * FROM fa_customers WHERE id = ? AND tenant_id = ?",
            [(int) $id, $this->tenantId]
        );

        if (!$customer) {
            $this->json(404, ['error' => 'Klant niet gevonden']);
            return;
        }

        $this->json(200, ['data' => $customer]);
    }

    private function json(int $status, array $payload): void
    {
        http_response_code($status);
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
