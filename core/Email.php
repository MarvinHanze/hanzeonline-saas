<?php
declare(strict_types=1);

namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $config = require __DIR__ . '/../config/email.php';

        $this->mailer->isSMTP();
        $this->mailer->Host = $config['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $config['username'];
        $this->mailer->Password = $config['password'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $config['port'];
        $this->mailer->CharSet = 'UTF-8';

        $this->mailer->setFrom($config['from'], $config['from_name']);
    }

    public function to(string $email, string $name = ''): self
    {
        $this->mailer->addAddress($email, $name);
        return $this;
    }

    public function subject(string $subject): self
    {
        $this->mailer->Subject = $subject;
        return $this;
    }

    public function html(string $html): self
    {
        $this->mailer->isHTML(true);
        $this->mailer->Body = $html;
        return $this;
    }

    public function text(string $text): self
    {
        $this->mailer->isHTML(false);
        $this->mailer->Body = $text;
        return $this;
    }

    public function attach(string $path, string $filename = ''): self
    {
        $this->mailer->addAttachment($path, $filename);
        return $this;
    }

    public function send(): bool
    {
        try {
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Email failed: " . $e->getMessage());
            return false;
        }
    }

    public static function invoiceReminder(array $invoice, array $customer, array $company, int $daysOverdue): bool
    {
        $email = new self();
        return $email
            ->to($customer['email'], $customer['name'])
            ->subject("Herinnering: Factuur #{$invoice['number']} ({$daysOverdue} dagen over)")
            ->html(self::renderReminderTemplate($invoice, $customer, $company, $daysOverdue))
            ->send();
    }

    private static function renderReminderTemplate(array $invoice, array $customer, array $company, int $days): string
    {
        // Defense-in-depth: klant-/bedrijfsnaam komen uit de database (door een
        // ingelogde facturatie.manage-gebruiker ingevoerd) en worden hier in een
        // HTML-e-mail geplaatst — escapen voorkomt dat een HTML/script-payload in
        // een klantnaam de e-mail-layout breekt of (bij een e-mailclient die HTML
        // uitvoert) iets onverwachts doet.
        $invoiceNumber = htmlspecialchars((string) $invoice['number']);
        $customerName = htmlspecialchars((string) $customer['name']);
        $companyName = htmlspecialchars((string) $company['name']);

        return "
        <html><body style='font-family:sans-serif;color:#333'>
        <h2>Herinnering: Factuur #{$invoiceNumber}</h2>
        <p>Beste {$customerName},</p>
        <p>Wij hebben uw betaling voor factuur <strong>#{$invoiceNumber}</strong> van <strong>€" . number_format((float)$invoice['total'], 2, ',', '.') . "</strong> nog niet ontvangen.</p>
        <p>Deze factuur is nu <strong>{$days} dagen</strong> over de vervaldatum heen.</p>
        <p>Betaal alstublieft zo snel mogelijk.</p>
        <p>Met vriendelijke groet,<br>{$companyName}</p>
        </body></html>";
    }
}
