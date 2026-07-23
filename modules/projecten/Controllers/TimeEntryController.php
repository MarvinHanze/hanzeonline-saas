<?php
declare(strict_types=1);

namespace Modules\Projecten\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

/** Urenregistratie: uren loggen op een project (+ optioneel een taak), en het eigen overzicht. */
class TimeEntryController
{
    public function store(string $id): void
    {
        Permission::require('projecten.view');
        $tenantId = (int) Auth::user()['tenant_id'];
        $userId = (int) Auth::user()['id'];

        $project = Database::fetch("SELECT id FROM projecten_projects WHERE id = ? AND tenant_id = ?", [(int) $id, $tenantId]);
        if (!$project) {
            header('Location: ' . BASE . '/projecten/projecten');
            exit;
        }

        $taskId = (int) ($_POST['task_id'] ?? 0);

        // Beveiliging: task_id moet een taak van DIT project (en dus deze tenant)
        // zijn — anders zou een gemanipuleerd id een urenregel kunnen koppelen aan
        // een taak van een ander project/tenant.
        if ($taskId > 0 && !Database::fetch("SELECT id FROM projecten_tasks WHERE id = ? AND tenant_id = ? AND project_id = ?", [$taskId, $tenantId, (int) $id])) {
            $taskId = 0;
        }

        Database::insert('projecten_time_entries', [
            'tenant_id' => $tenantId,
            'project_id' => (int) $id,
            'task_id' => $taskId > 0 ? $taskId : null,
            'user_id' => $userId,
            'entry_date' => $_POST['entry_date'] ?: date('Y-m-d'),
            'hours' => (float) ($_POST['hours'] ?? 0),
            'description' => trim($_POST['description'] ?? ''),
            'billable' => isset($_POST['billable']) ? 1 : 0,
        ]);

        header('Location: ' . BASE . '/projecten/projecten/' . $id);
        exit;
    }

    /** Persoonlijk urenoverzicht (eigen uren over alle projecten). */
    public function index(): void
    {
        Permission::require('projecten.view');
        $tenantId = (int) Auth::user()['tenant_id'];
        $userId = (int) Auth::user()['id'];

        $entries = Database::fetchAll(
            "SELECT te.*, p.name AS project_name
             FROM projecten_time_entries te
             JOIN projecten_projects p ON te.project_id = p.id
             WHERE te.tenant_id = ? AND te.user_id = ?
             ORDER BY te.entry_date DESC LIMIT 50",
            [$tenantId, $userId]
        );

        $totalThisWeek = Database::fetch(
            "SELECT COALESCE(SUM(hours), 0) AS total FROM projecten_time_entries
             WHERE tenant_id = ? AND user_id = ? AND YEARWEEK(entry_date, 1) = YEARWEEK(CURDATE(), 1)",
            [$tenantId, $userId]
        )['total'];

        View::render('modules/projecten/views/time_entries/index', [
            'entries' => $entries,
            'totalThisWeek' => (float) $totalThisWeek,
        ]);
    }
}
