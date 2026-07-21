<?php
declare(strict_types=1);

namespace Modules\Voorraad\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

/** Materieelbeheer: gereedschap/apparatuur bijhouden (status, locatie, wie het gebruikt). */
class EquipmentController
{
    private const STATUSES = ['beschikbaar', 'in_gebruik', 'onderhoud', 'defect'];

    public function index(): void
    {
        Permission::require('voorraad.view');
        $tenantId = (int) Auth::user()['tenant_id'];

        $equipment = Database::fetchAll(
            "SELECT * FROM voorraad_equipment WHERE tenant_id = ? ORDER BY name",
            [$tenantId]
        );

        View::render('modules/voorraad/views/equipment/index', ['equipment' => $equipment, 'statuses' => self::STATUSES]);
    }

    public function store(): void
    {
        Permission::require('voorraad.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        Database::insert('voorraad_equipment', [
            'tenant_id' => $tenantId,
            'name' => trim($_POST['name'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'serial_number' => trim($_POST['serial_number'] ?? ''),
            'status' => 'beschikbaar',
            'location' => trim($_POST['location'] ?? ''),
            'purchase_date' => $_POST['purchase_date'] ?: null,
        ]);

        header('Location: ' . BASE . '/voorraad/materieel');
        exit;
    }

    public function updateStatus(string $id): void
    {
        Permission::require('voorraad.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        $status = $_POST['status'] ?? '';

        if (!in_array($status, self::STATUSES, true)) {
            header('Location: ' . BASE . '/voorraad/materieel');
            exit;
        }

        $assignedTo = trim($_POST['assigned_to'] ?? '');
        $data = [
            'status' => $status,
            'assigned_to' => $status === 'in_gebruik' ? ($assignedTo ?: null) : null,
        ];

        Database::update('voorraad_equipment', $data, 'id = ? AND tenant_id = ?', [(int) $id, $tenantId]);
        header('Location: ' . BASE . '/voorraad/materieel');
        exit;
    }
}
