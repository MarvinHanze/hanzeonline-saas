<?php
declare(strict_types=1);

namespace Modules\Projecten\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

class ProjectController
{
    private const STATUSES = ['gepland', 'actief', 'on_hold', 'afgerond', 'geannuleerd'];

    public function index(): void
    {
        Permission::require('projecten.view');
        $tenantId = (int) Auth::user()['tenant_id'];
        $status = $_GET['status'] ?? '';

        $where = ['tenant_id = ?'];
        $params = [$tenantId];
        if (in_array($status, self::STATUSES, true)) {
            $where[] = 'status = ?';
            $params[] = $status;
        }

        $projects = Database::fetchAll(
            "SELECT * FROM projecten_projects WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC",
            $params
        );

        View::render('modules/projecten/views/projects/index', [
            'projects' => $projects,
            'status' => $status,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(): void
    {
        Permission::require('projecten.manage');
        View::render('modules/projecten/views/projects/create', ['statuses' => self::STATUSES]);
    }

    public function store(): void
    {
        Permission::require('projecten.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $id = Database::insert('projecten_projects', [
            'tenant_id' => $tenantId,
            'name' => trim($_POST['name'] ?? ''),
            'client_name' => trim($_POST['client_name'] ?? ''),
            'status' => in_array($_POST['status'] ?? '', self::STATUSES, true) ? $_POST['status'] : 'gepland',
            'start_date' => $_POST['start_date'] ?: null,
            'end_date' => $_POST['end_date'] ?: null,
            'budget_hours' => (float) ($_POST['budget_hours'] ?? 0),
            'budget_amount' => (float) ($_POST['budget_amount'] ?? 0),
            'description' => trim($_POST['description'] ?? ''),
        ]);

        header('Location: ' . BASE . '/projecten/projecten/' . $id);
        exit;
    }

    public function show(string $id): void
    {
        Permission::require('projecten.view');
        $tenantId = (int) Auth::user()['tenant_id'];

        $project = Database::fetch("SELECT * FROM projecten_projects WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
        if (!$project) {
            http_response_code(404);
            echo 'Project niet gevonden';
            return;
        }

        $tasks = Database::fetchAll(
            "SELECT t.*, u.name AS assignee_name
             FROM projecten_tasks t
             LEFT JOIN users u ON t.assignee_id = u.id
             WHERE t.project_id = ? AND t.tenant_id = ?
             ORDER BY (t.status = 'klaar'), t.due_date IS NULL, t.due_date ASC",
            [(int) $id, $tenantId]
        );

        $timeEntries = Database::fetchAll(
            "SELECT te.*, u.name AS user_name
             FROM projecten_time_entries te
             JOIN users u ON te.user_id = u.id
             WHERE te.project_id = ? AND te.tenant_id = ?
             ORDER BY te.entry_date DESC LIMIT 20",
            [(int) $id, $tenantId]
        );

        $totalHours = Database::fetch(
            "SELECT COALESCE(SUM(hours), 0) AS total FROM projecten_time_entries WHERE project_id = ? AND tenant_id = ?",
            [(int) $id, $tenantId]
        )['total'];

        $teamMembers = Database::fetchAll("SELECT id, name FROM users WHERE tenant_id = ? ORDER BY name", [$tenantId]);

        View::render('modules/projecten/views/projects/show', [
            'project' => $project,
            'tasks' => $tasks,
            'timeEntries' => $timeEntries,
            'totalHours' => (float) $totalHours,
            'teamMembers' => $teamMembers,
        ]);
    }

    public function edit(string $id): void
    {
        Permission::require('projecten.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $project = Database::fetch("SELECT * FROM projecten_projects WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
        if (!$project) {
            http_response_code(404);
            echo 'Project niet gevonden';
            return;
        }

        View::render('modules/projecten/views/projects/edit', ['project' => $project, 'statuses' => self::STATUSES]);
    }

    public function update(string $id): void
    {
        Permission::require('projecten.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        Database::update('projecten_projects', [
            'name' => trim($_POST['name'] ?? ''),
            'client_name' => trim($_POST['client_name'] ?? ''),
            'status' => in_array($_POST['status'] ?? '', self::STATUSES, true) ? $_POST['status'] : 'gepland',
            'start_date' => $_POST['start_date'] ?: null,
            'end_date' => $_POST['end_date'] ?: null,
            'budget_hours' => (float) ($_POST['budget_hours'] ?? 0),
            'budget_amount' => (float) ($_POST['budget_amount'] ?? 0),
            'description' => trim($_POST['description'] ?? ''),
        ], 'id = ? AND tenant_id = ?', [(int) $id, $tenantId]);

        header('Location: ' . BASE . '/projecten/projecten/' . $id);
        exit;
    }
}
