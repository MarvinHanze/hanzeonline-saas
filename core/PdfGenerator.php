<?php
declare(strict_types=1);

namespace Core;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    private Dompdf $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'sans-serif');
        $this->dompdf = new Dompdf($options);
    }

    public function loadHtml(string $html): void
    {
        $this->dompdf->loadHtml($html);
    }

    public function loadView(string $viewPath, array $data = []): void
    {
        extract($data);
        ob_start();
        require $viewPath;
        $html = ob_get_clean();
        $this->loadHtml($html);
    }

    public function setPaper(string $size = 'A4', string $orientation = 'portrait'): void
    {
        $this->dompdf->setPaper($size, $orientation);
    }

    public function render(): void
    {
        $this->dompdf->render();
    }

    public function output(): string
    {
        return $this->dompdf->output();
    }

    public function stream(string $filename = 'document.pdf'): void
    {
        $this->dompdf->stream($filename, ['Attachment' => false]);
    }

    public function download(string $filename = 'document.pdf'): void
    {
        $this->dompdf->stream($filename, ['Attachment' => true]);
    }

    public function save(string $path): void
    {
        $this->render();
        file_put_contents($path, $this->output());
    }

    public static function invoice(array $invoice, array $items, array $company): string
    {
        $pdf = new self();
        $pdf->setPaper('A4', 'portrait');
        $pdf->loadView(__DIR__ . '/../modules/facturatie/pdf/invoice.php', [
            'invoice' => $invoice,
            'items' => $items,
            'company' => $company,
        ]);
        $pdf->render();
        return $pdf->output();
    }

    public static function contract(array $contract, array $company): string
    {
        $pdf = new self();
        $pdf->setPaper('A4', 'portrait');
        $pdf->loadView(__DIR__ . '/../modules/contract/pdf/contract.php', [
            'contract' => $contract,
            'company' => $company,
        ]);
        $pdf->render();
        return $pdf->output();
    }
}
