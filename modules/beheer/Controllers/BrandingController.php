<?php
declare(strict_types=1);

namespace Modules\Beheer\Controllers;

use Core\Auth;
use Core\Database;
use Core\Permission;
use Core\Tenant;
use Core\View;

class BrandingController
{
    public function index(): void
    {
        Permission::require('beheer.manage');
        View::render('modules/beheer/views/branding/index', [
            'tenant' => Tenant::get(),
        ]);
    }

    public function update(): void
    {
        Permission::require('beheer.manage');
        $tenantId = (int) Auth::user()['tenant_id'];

        $color = trim($_POST['brand_color'] ?? '');
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $color = Tenant::brandColor();
        }
        $data = ['brand_color' => $color];

        if (!empty($_FILES['logo']['name']) && ($_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            // Let op: bewust GEEN image/svg+xml — een SVG-logo is direct benaderbaar
            // via een URL binnen public/uploads/branding/ (geen auth/tenant-check op
            // die directe bestands-URL) en SVG kan <script>/event-handlers bevatten,
            // wat bij directe navigatie (niet via <img>) alsnog in de browser kan
            // uitvoeren — een opgeslagen-XSS-vector. Rasterformaten zijn voldoende
            // voor een logo.
            $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp'];
            $mime = (string) mime_content_type($_FILES['logo']['tmp_name']);
            if (isset($allowed[$mime]) && $_FILES['logo']['size'] <= 2 * 1024 * 1024) {
                $filename = 'tenant_' . $tenantId . '_' . time() . '.' . $allowed[$mime];
                $dest = __DIR__ . '/../../../public/uploads/branding/' . $filename;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest)) {
                    $data['logo_path'] = 'uploads/branding/' . $filename;
                }
            }
        }

        Database::update('tenants', $data, 'id = ?', [$tenantId]);
        Tenant::load($tenantId);

        header('Location: ' . BASE . '/beheer/branding');
        exit;
    }
}
