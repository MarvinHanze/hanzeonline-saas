<?php
declare(strict_types=1);

namespace Modules\Contract\Controllers;

use Core\Database;
use Core\Auth;
use Core\View;
use Core\Signature;

class ContractController
{
    public function index(): void
    {
        $tenantId = Auth::user()['tenant_id'];
        $status = $_GET['status'] ?? '';

        $where = 'c.tenant_id = ?';
        $params = [$tenantId];

        if ($status && in_array($status, ['concept', 'actief', 'verlopen', 'vernieuwd', 'geannuleerd'])) {
            $where .= ' AND c.status = ?';
            $params[] = $status;
        }

        $contracts = Database::fetchAll(
            "SELECT c.*, e.name as employee_name, cu.name as customer_name
             FROM ct_contracts c
             LEFT JOIN hr_employees e ON c.employee_id = e.id
             LEFT JOIN fa_customers cu ON c.customer_id = cu.id
             WHERE $where
             ORDER BY c.created_at DESC",
            $params
        );

        View::render('modules/contract/views/contracts/index', [
            'contracts' => $contracts,
            'currentStatus' => $status,
        ]);
    }

    public function create(): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $templates = Database::fetchAll(
            "SELECT id, name FROM ct_templates WHERE tenant_id = ? ORDER BY name",
            [$tenantId]
        );

        $customers = Database::fetchAll(
            "SELECT id, name FROM fa_customers WHERE tenant_id = ? ORDER BY name",
            [$tenantId]
        );

        $employees = Database::fetchAll(
            "SELECT id, name FROM hr_employees WHERE tenant_id = ? AND status = 'actief' ORDER BY name",
            [$tenantId]
        );

        View::render('modules/contract/views/contracts/create', [
            'templates' => $templates,
            'customers' => $customers,
            'employees' => $employees,
        ]);
    }

    public function store(): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $templateId = (int) ($_POST['template_id'] ?? 0);
        $template = $templateId ? Database::fetch(
            "SELECT * FROM ct_templates WHERE id = ? AND tenant_id = ?",
            [$templateId, $tenantId]
        ) : null;

        $title = trim($_POST['title'] ?? '');
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;
        $customerId = (int) ($_POST['customer_id'] ?? 0) ?: null;
        $employeeId = (int) ($_POST['employee_id'] ?? 0) ?: null;
        $notes = trim($_POST['notes'] ?? '');

        // Replace template variables if a template was selected
        $content = $template ? $this->replaceVariables($template['content_html'], [
            'klant_naam' => $customerId ? $this->getName('fa_customers', $customerId, $tenantId) : '',
            'medewerker_naam' => $employeeId ? $this->getName('hr_employees', $employeeId, $tenantId) : '',
            'datum' => date('d-m-Y'),
            'start_datum' => $startDate ?: '',
            'eind_datum' => $endDate ?: '',
            'bedrijf_naam' => \Core\Tenant::name(),
        ]) : $notes;

        $contractId = Database::insert('ct_contracts', [
            'tenant_id' => $tenantId,
            'template_id' => $templateId ?: null,
            'customer_id' => $customerId,
            'employee_id' => $employeeId,
            'title' => $title,
            'status' => 'concept',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'signature_data' => $content,
        ]);

        header('Location: ' . BASE . '/contract/contracts/$contractId");
        exit;
    }

    public function show(string $id): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $contract = Database::fetch(
            "SELECT c.*, e.name as employee_name, cu.name as customer_name
             FROM ct_contracts c
             LEFT JOIN hr_employees e ON c.employee_id = e.id
             LEFT JOIN fa_customers cu ON c.customer_id = cu.id
             WHERE c.id = ? AND c.tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$contract) {
            http_response_code(404);
            echo 'Contract niet gevonden';
            return;
        }

        // Rendered content from template
        $renderedContent = $contract['signature_data'] ?? '';
        if ($contract['template_id'] && $renderedContent) {
            $template = Database::fetch(
                "SELECT content_html FROM ct_templates WHERE id = ?",
                [$contract['template_id']]
            );
            if ($template) {
                $renderedContent = $this->replaceVariables($template['content_html'], [
                    'klant_naam' => $contract['customer_name'] ?? '',
                    'medewerker_naam' => $contract['employee_name'] ?? '',
                    'datum' => date('d-m-Y'),
                    'start_datum' => $contract['start_date'] ? date('d-m-Y', strtotime($contract['start_date'])) : '',
                    'eind_datum' => $contract['end_date'] ? date('d-m-Y', strtotime($contract['end_date'])) : '',
                    'bedrijf_naam' => \Core\Tenant::name(),
                ]);
            }
        }

        $hasSignature = Signature::hasSignature((int) $id);
        $signaturePath = Signature::getSignaturePath((int) $id);

        View::render('modules/contract/views/contracts/show', [
            'contract' => $contract,
            'renderedContent' => $renderedContent,
            'hasSignature' => $hasSignature,
            'signaturePath' => $signaturePath,
        ]);
    }

    public function pdf(string $id): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $contract = Database::fetch(
            "SELECT c.*, e.name as employee_name, cu.name as customer_name
             FROM ct_contracts c
             LEFT JOIN hr_employees e ON c.employee_id = e.id
             LEFT JOIN fa_customers cu ON c.customer_id = cu.id
             WHERE c.id = ? AND c.tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$contract) {
            http_response_code(404);
            echo 'Contract niet gevonden';
            return;
        }

        // Rendered content
        $renderedContent = '';
        if ($contract['template_id']) {
            $template = Database::fetch(
                "SELECT content_html FROM ct_templates WHERE id = ?",
                [$contract['template_id']]
            );
            if ($template) {
                $renderedContent = $this->replaceVariables($template['content_html'], [
                    'klant_naam' => $contract['customer_name'] ?? '',
                    'medewerker_naam' => $contract['employee_name'] ?? '',
                    'datum' => date('d-m-Y'),
                    'start_datum' => $contract['start_date'] ? date('d-m-Y', strtotime($contract['start_date'])) : '',
                    'eind_datum' => $contract['end_date'] ? date('d-m-Y', strtotime($contract['end_date'])) : '',
                    'bedrijf_naam' => \Core\Tenant::name(),
                ]);
            }
        }

        $pdfDir = __DIR__ . '/../../../storage/contracts';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0775, true);
        }

        $html = '<!DOCTYPE html><html><head>
    <base href="/saas-platform/"><meta charset="utf-8"><style>
            body{font-family:sans-serif;padding:40px;color:#1e293b;}
            h1{font-size:22px;margin-bottom:8px;}
            .meta{color:#64748b;margin-bottom:24px;font-size:14px;}
            .content{line-height:1.6;}
            .signature{margin-top:40px;}
            .signature img{max-width:250px;border:1px solid #e2e8f0;}
        </style></head><body>
            <h1>' . htmlspecialchars($contract['title']) . '</h1>
            <div class="meta">
                Status: ' . htmlspecialchars($contract['status']) . '<br>
                Start: ' . ($contract['start_date'] ?? '-') . ' &mdash; Eind: ' . ($contract['end_date'] ?? '-') .
                ($contract['signed_at'] ? '<br>Ondertekend: ' . date('d-m-Y H:i', strtotime($contract['signed_at'])) : '') .
            '</div>
            <div class="content">' . $renderedContent . '</div>';

        $sigPath = Signature::getSignaturePath((int) $id);
        if ($sigPath && file_exists($sigPath)) {
            $sigData = base64_encode(file_get_contents($sigPath));
            $html .= '<div class="signature"><p><strong>Handtekening:</strong></p><img src="data:image/png;base64,' . $sigData . '" alt="Handtekening"></div>';
        }

        $html .= '</body></html>';

        $pdfFile = $pdfDir . '/contract_' . $id . '.pdf';

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream('contract_' . $contract['title'] . '.pdf', ['Attachment' => true]);

        // Update pdf_path
        Database::update('ct_contracts', ['pdf_path' => $pdfFile], 'id = ?', [$id]);
        exit;
    }

    public function sign(string $id): void
    {
        $tenantId = Auth::user()['tenant_id'];

        $contract = Database::fetch(
            "SELECT * FROM ct_contracts WHERE id = ? AND tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$contract) {
            http_response_code(404);
            echo 'Contract niet gevonden';
            return;
        }

        $signatureData = $_POST['signature_data'] ?? '';
        if (!$signatureData) {
            header('Location: ' . BASE . '/contract/contracts/$id");
            exit;
        }

        $sigDir = __DIR__ . '/../../../storage/signatures';
        $imagePath = Signature::saveFromCanvas($signatureData, $sigDir);
        $user = Auth::user();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        Signature::saveSignatureRecord((int) $id, $imagePath, $user['name'], $ip);

        Database::update('ct_contracts', [
            'signed_at' => date('Y-m-d H:i:s'),
            'signed_by' => $user['name'],
            'status' => 'actief',
        ], 'id = ?', [$id]);

        header('Location: ' . BASE . '/contract/contracts/$id");
        exit;
    }

    public function updateStatus(string $id): void
    {
        $tenantId = Auth::user()['tenant_id'];
        $status = $_POST['status'] ?? '';

        $allowed = ['concept', 'actief', 'verlopen', 'vernieuwd', 'geannuleerd'];
        if (!in_array($status, $allowed)) {
            header('Location: ' . BASE . '/contract/contracts/$id");
            exit;
        }

        Database::update('ct_contracts', ['status' => $status], 'id = ? AND tenant_id = ?', [$id, $tenantId]);
        header('Location: ' . BASE . '/contract/contracts/$id");
        exit;
    }

    private function replaceVariables(string $html, array $vars): string
    {
        foreach ($vars as $key => $value) {
            $html = str_replace('{{' . $key . '}}', htmlspecialchars((string) $value), $html);
        }
        return $html;
    }

    private function getName(string $table, int $id, int $tenantId): string
    {
        $row = Database::fetch("SELECT name FROM $table WHERE id = ? AND tenant_id = ?", [$id, $tenantId]);
        return $row['name'] ?? '';
    }
}
