<?php
declare(strict_types=1);

namespace Modules\Hr\Controllers;

use Core\Auth;
use Core\Database;
use Core\View;

class EmployeeController
{
    public function index(): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];
        $search = trim($_GET['search'] ?? '');
        $deptId = (int) ($_GET['department'] ?? 0);

        $where = ['e.tenant_id = ?'];
        $params = [$tenantId];

        if ($search !== '') {
            $where[] = '(e.name LIKE ? OR e.email LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($deptId > 0) {
            $where[] = 'e.department_id = ?';
            $params[] = $deptId;
        }

        $sql = "SELECT e.*, d.name AS department_name
                FROM hr_employees e
                LEFT JOIN hr_departments d ON e.department_id = d.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY e.name ASC";

        $employees = Database::fetchAll($sql, $params);
        $departments = Database::fetchAll(
            "SELECT id, name FROM hr_departments WHERE tenant_id = ? ORDER BY name",
            [$tenantId]
        );

        View::render('hr.views.employees.index', [
            'employees' => $employees,
            'departments' => $departments,
            'search' => $search,
            'departmentId' => $deptId,
        ]);
    }

    public function create(): void
    {
        $departments = Database::fetchAll(
            "SELECT id, name FROM hr_departments WHERE tenant_id = ? ORDER BY name",
            [(int) Auth::user()['tenant_id']]
        );
        View::render('hr.views.employees.create', ['departments' => $departments]);
    }

    public function store(): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];

        Database::insert('hr_employees', [
            'tenant_id' => $tenantId,
            'department_id' => (int) ($_POST['department_id'] ?: 0) ?: null,
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'position' => trim($_POST['position']),
            'salary' => (float) ($_POST['salary'] ?? 0),
            'start_date' => $_POST['start_date'],
            'contract_end' => $_POST['contract_end'] ?: null,
            'status' => 'actief',
            'leave_balance_days' => (int) ($_POST['leave_balance_days'] ?? 25),
        ]);

        header('Location: /hr/medewerkers');
        exit;
    }

    public function show(string $id): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];

        $employee = Database::fetch(
            "SELECT e.*, d.name AS department_name
             FROM hr_employees e
             LEFT JOIN hr_departments d ON e.department_id = d.id
             WHERE e.id = ? AND e.tenant_id = ?",
            [(int) $id, $tenantId]
        );
        if (!$employee) {
            http_response_code(404);
            echo 'Medewerker niet gevonden';
            return;
        }

        $leaveHistory = Database::fetchAll(
            "SELECT * FROM hr_leave_requests WHERE employee_id = ? AND tenant_id = ? ORDER BY start_date DESC",
            [(int) $id, $tenantId]
        );

        $reviews = Database::fetchAll(
            "SELECT r.*, u.name AS reviewer_name
             FROM hr_reviews r
             LEFT JOIN users u ON r.reviewer_id = u.id
             WHERE r.employee_id = ? AND r.tenant_id = ?
             ORDER BY r.created_at DESC",
            [(int) $id, $tenantId]
        );

        View::render('hr.views.employees.show', [
            'employee' => $employee,
            'leaveHistory' => $leaveHistory,
            'reviews' => $reviews,
        ]);
    }

    public function edit(string $id): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];

        $employee = Database::fetch(
            "SELECT * FROM hr_employees WHERE id = ? AND tenant_id = ?",
            [(int) $id, $tenantId]
        );
        if (!$employee) {
            http_response_code(404);
            echo 'Medewerker niet gevonden';
            return;
        }

        $departments = Database::fetchAll(
            "SELECT id, name FROM hr_departments WHERE tenant_id = ? ORDER BY name",
            [$tenantId]
        );

        View::render('hr.views.employees.edit', [
            'employee' => $employee,
            'departments' => $departments,
        ]);
    }

    public function update(string $id): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];

        Database::update('hr_employees', [
            'department_id' => (int) ($_POST['department_id'] ?: 0) ?: null,
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'position' => trim($_POST['position']),
            'salary' => (float) ($_POST['salary'] ?? 0),
            'start_date' => $_POST['start_date'],
            'contract_end' => $_POST['contract_end'] ?: null,
            'status' => $_POST['status'],
            'leave_balance_days' => (int) ($_POST['leave_balance_days'] ?? 25),
        ], 'id = ? AND tenant_id = ?', [(int) $id, $tenantId]);

        header("Location: /hr/medewerkers/$id");
        exit;
    }
}
