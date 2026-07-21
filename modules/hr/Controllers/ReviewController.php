<?php
declare(strict_types=1);

namespace Modules\Hr\Controllers;

use Core\Auth;
use Core\Database;
use Core\View;

class ReviewController
{
    public function index(): void
    {
        $tenantId = (int) Auth::user()['tenant_id'];

        $reviews = Database::fetchAll(
            "SELECT r.*, e.name AS employee_name, u.name AS reviewer_name
             FROM hr_reviews r
             JOIN hr_employees e ON r.employee_id = e.id
             LEFT JOIN users u ON r.reviewer_id = u.id
             WHERE r.tenant_id = ?
             ORDER BY r.created_at DESC",
            [$tenantId]
        );

        View::render('hr.views.reviews.index', ['reviews' => $reviews]);
    }
}
