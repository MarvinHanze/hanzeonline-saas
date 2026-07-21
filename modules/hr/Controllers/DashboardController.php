<?php
declare(strict_types=1);

namespace Modules\Hr\Controllers;

use Core\Auth;
use Core\Database;
use Core\View;

class DashboardController
{
    public function index(): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];

        $totalEmployees = Database::count('hr_employees', 'tenant_id = ?', [$tenantId]);
        $activeLeave = Database::count('hr_leave_requests', 'tenant_id = ? AND status = ?', [$tenantId, 'ingediend']);
        $pendingReviews = Database::count('hr_reviews', 'tenant_id = ? AND status = ?', [$tenantId, 'gepland']);
        $departments = Database::count('hr_departments', 'tenant_id = ?', [$tenantId]);

        $recentLeave = Database::fetchAll(
            "SELECT lr.*, e.name AS employee_name
             FROM hr_leave_requests lr
             JOIN hr_employees e ON lr.employee_id = e.id
             WHERE lr.tenant_id = ?
             ORDER BY lr.created_at DESC
             LIMIT 10",
            [$tenantId]
        );

        View::render('modules/hr/views.dashboard.index', [
            'totalEmployees' => $totalEmployees,
            'activeLeave' => $activeLeave,
            'pendingReviews' => $pendingReviews,
            'departments' => $departments,
            'recentLeave' => $recentLeave,
        ]);
    }
}
