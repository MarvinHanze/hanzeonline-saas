<?php
declare(strict_types=1);

namespace Modules\Hr\Controllers;

use Core\Auth;
use Core\Database;
use Core\View;

class DepartmentController
{
    public function index(): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];

        $departments = Database::fetchAll(
            "SELECT d.*, m.name AS manager_name,
                    (SELECT COUNT(*) FROM hr_employees e WHERE e.department_id = d.id AND e.status = 'actief') AS employee_count
             FROM hr_departments d
             LEFT JOIN hr_employees m ON d.manager_id = m.id
             WHERE d.tenant_id = ?
             ORDER BY d.name",
            [$tenantId]
        );

        View::render('modules/hr/views.departments.index', ['departments' => $departments]);
    }
}
