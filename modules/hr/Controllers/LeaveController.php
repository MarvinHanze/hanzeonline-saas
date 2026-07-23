<?php
declare(strict_types=1);

namespace Modules\Hr\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

class LeaveController
{
    public function index(): void
    {
        Permission::require('hr.view');
        $tenantId = (int) Auth::user()['tenant_id'];
        $status = $_GET['status'] ?? '';
        $isAdmin = Auth::isAdmin();

        $where = ['lr.tenant_id = ?'];
        $params = [$tenantId];

        if (!$isAdmin) {
            $employee = Database::fetch(
                "SELECT id FROM hr_employees WHERE tenant_id = ? AND user_id = ?",
                [$tenantId, (int) Auth::user()['id']]
            );
            $where[] = 'lr.employee_id = ?';
            $params[] = $employee['id'] ?? 0;
        }

        if ($status !== '' && in_array($status, ['ingediend', 'goedgekeurd', 'afgewezen'])) {
            $where[] = 'lr.status = ?';
            $params[] = $status;
        }

        $sql = "SELECT lr.*, e.name AS employee_name
                FROM hr_leave_requests lr
                JOIN hr_employees e ON lr.employee_id = e.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY lr.created_at DESC";

        $requests = Database::fetchAll($sql, $params);

        $employees = Database::fetchAll(
            "SELECT id, name, leave_balance_days FROM hr_employees WHERE tenant_id = ? AND status = 'actief' ORDER BY name",
            [$tenantId]
        );

        View::render('modules/hr/views.leave.index', [
            'requests' => $requests,
            'employees' => $employees,
            'status' => $status,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function store(): void
    {
        Permission::require('hr.view');
        $tenantId = (int) Auth::user()['tenant_id'];
        $employeeId = (int) ($_POST['employee_id'] ?? 0);

        // Beveiliging: een niet-admin mag alleen verlof aanvragen voor het EIGEN
        // medewerkersdossier, niet voor een willekeurig employee_id uit de POST
        // (zou anders een IDOR zijn — verlof aanvragen namens een collega).
        if (!Auth::isAdmin()) {
            $ownEmployee = Database::fetch(
                "SELECT id FROM hr_employees WHERE tenant_id = ? AND user_id = ?",
                [$tenantId, (int) Auth::user()['id']]
            );
            $employeeId = (int) ($ownEmployee['id'] ?? 0);
        }

        $employee = Database::fetch(
            "SELECT id, leave_balance_days FROM hr_employees WHERE id = ? AND tenant_id = ?",
            [$employeeId, $tenantId]
        );

        if (!$employee) {
            header('Location: ' . BASE . '/hr/verlof?error=no_employee_record');
            exit;
        }

        $days = (int) $_POST['days'];
        if ($employee && $days > $employee['leave_balance_days']) {
            header('Location: ' . BASE . '/hr/verlof?error=insufficient_balance');
            exit;
        }

        Database::insert('hr_leave_requests', [
            'tenant_id' => $tenantId,
            'employee_id' => $employeeId,
            'type' => $_POST['type'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'days' => $days,
            'notes' => trim($_POST['notes'] ?? ''),
        ]);

        header('Location: ' . BASE . '/hr/verlof');
        exit;
    }

    public function approve(string $id): void
    {
        Permission::require('hr.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $request = Database::fetch(
            "SELECT * FROM hr_leave_requests WHERE id = ? AND tenant_id = ? AND status = 'ingediend'",
            [(int) $id, $tenantId]
        );

        if (!$request) {
            header('Location: ' . BASE . '/hr/verlof');
            exit;
        }

        Database::update('hr_leave_requests', [
            'status' => 'goedgekeurd',
            'approved_by' => (int) Auth::user()['id'],
        ], 'id = ? AND tenant_id = ?', [(int) $id, $tenantId]);

        Database::query(
            "UPDATE hr_employees SET leave_balance_days = leave_balance_days - ? WHERE id = ? AND tenant_id = ?",
            [$request['days'], $request['employee_id'], $tenantId]
        );

        header('Location: ' . BASE . '/hr/verlof');
        exit;
    }

    public function reject(string $id): void
    {
        Permission::require('hr.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        Database::update('hr_leave_requests', [
            'status' => 'afgewezen',
            'notes' => trim($_POST['reason'] ?? ''),
        ], 'id = ? AND tenant_id = ? AND status = ?', [(int) $id, $tenantId, 'ingediend']);

        header('Location: ' . BASE . '/hr/verlof');
        exit;
    }
}
