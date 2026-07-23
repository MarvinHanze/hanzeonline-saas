<?php
declare(strict_types=1);

namespace Modules\Projecten\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;

class TaskController
{
    private const STATUSES = ['open', 'bezig', 'klaar'];

    /** Voegt een taak toe aan project {id} (taakverdeling). */
    public function store(string $id): void
    {
        Permission::require('projecten.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $project = Database::fetch("SELECT id FROM projecten_projects WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
        if (!$project) {
            header('Location: ' . BASE . '/projecten/projecten');
            exit;
        }

        $assigneeId = (int) ($_POST['assignee_id'] ?? 0);

        // Beveiliging: assignee_id moet een user van DEZE tenant zijn — anders zou
        // een gemanipuleerd id een taak kunnen koppelen aan een gebruiker van een
        // andere tenant (diens naam lekt dan mee via de LEFT JOIN in
        // ProjectController::show()).
        if ($assigneeId > 0 && !Database::fetch("SELECT id FROM users WHERE id = ? AND tenant_id = ?", [$assigneeId, $tenantId])) {
            $assigneeId = 0;
        }

        Database::insert('projecten_tasks', [
            'tenant_id' => $tenantId,
            'project_id' => (int) $id,
            'title' => trim($_POST['title'] ?? ''),
            'assignee_id' => $assigneeId > 0 ? $assigneeId : null,
            'status' => 'open',
            'due_date' => $_POST['due_date'] ?: null,
        ]);

        header('Location: ' . BASE . '/projecten/projecten/' . $id);
        exit;
    }

    /** Wisselt de status van een taak (planning-workflow: open -> bezig -> klaar). */
    public function updateStatus(string $id): void
    {
        Permission::require('projecten.manage');
        $tenantId = (int) Auth::user()['tenant_id'];
        $status = $_POST['status'] ?? '';

        $task = Database::fetch("SELECT * FROM projecten_tasks WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
        if (!$task || !in_array($status, self::STATUSES, true)) {
            header('Location: ' . BASE . '/projecten/projecten');
            exit;
        }

        Database::update('projecten_tasks', ['status' => $status], 'id = ? AND tenant_id = ?', [(int) $id, $tenantId]);

        header('Location: ' . BASE . '/projecten/projecten/' . $task['project_id']);
        exit;
    }
}
