<?php
declare(strict_types=1);

namespace Modules\Projecten\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\View;

class DashboardController
{
    public function index(): void
    {
        Permission::require('projecten.view');
        $tenantId = (int) Auth::user()['tenant_id'];

        $activeProjects = Database::count('projecten_projects', "tenant_id = ? AND status = 'actief'", [$tenantId]);
        $openTasks = Database::count(
            'projecten_tasks',
            "tenant_id = ? AND status != 'klaar'",
            [$tenantId]
        );
        $overdueTasks = Database::count(
            'projecten_tasks',
            "tenant_id = ? AND status != 'klaar' AND due_date IS NOT NULL AND due_date < CURDATE()",
            [$tenantId]
        );
        $hoursThisWeek = Database::fetch(
            "SELECT COALESCE(SUM(hours), 0) AS total FROM projecten_time_entries
             WHERE tenant_id = ? AND YEARWEEK(entry_date, 1) = YEARWEEK(CURDATE(), 1)",
            [$tenantId]
        )['total'];

        $recentProjects = Database::fetchAll(
            "SELECT * FROM projecten_projects WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 8",
            [$tenantId]
        );

        View::render('modules/projecten/views/dashboard/index', [
            'activeProjects' => $activeProjects,
            'openTasks' => $openTasks,
            'overdueTasks' => $overdueTasks,
            'hoursThisWeek' => (float) $hoursThisWeek,
            'recentProjects' => $recentProjects,
        ]);
    }
}
