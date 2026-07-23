<!DOCTYPE html>
<html>
<head>
    <base href="/saas-platform/">
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #3b82f6; padding-bottom: 20px; }
        .company h1 { font-size: 18px; color: #1e3a5f; margin: 0 0 5px; }
        .company p { margin: 2px 0; font-size: 10px; color: #666; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { font-size: 24px; color: #3b82f6; margin: 0; }
        .invoice-title p { margin: 2px 0; font-size: 10px; color: #666; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { width: 48%; }
        .info-box h3 { font-size: 10px; text-transform: uppercase; color: #999; margin: 0 0 8px; letter-spacing: 0.05em; }
        .info-box p { margin: 2px 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f1f5f9; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        .text-right { text-align: right; }
        .totals { width: 300px; margin-left: auto; }
        .totals table { margin-bottom: 0; }
        .totals td { padding: 5px 10px; }
        .totals .total-row { font-weight: bold; font-size: 13px; border-top: 2px solid #3b82f6; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #999; text-align: center; }
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .status-concept { background: #fef3c7; color: #92400e; }
        .status-verstuurd { background: #dbeafe; color: #1e40af; }
        .status-betaald { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">
            <h1><?= htmlspecialchars($company['name']) ?></h1>
            <p><?= htmlspecialchars($company['address'] ?? '') ?></p>
            <p>KvK: <?= htmlspecialchars($company['kvk'] ?? '') ?></p>
            <p>BTW: <?= htmlspecialchars($company['btw_nr'] ?? '') ?></p>
        </div>
        <div class="invoice-title">
            <h2>FACTUUR</h2>
            <p><strong>#<?= htmlspecialchars($invoice['number']) ?></strong></p>
            <p>Datum: <?= date('d-m-Y', strtotime($invoice['created_at'])) ?></p>
            <p>Vervaldatum: <?= $invoice['due_date'] ? date('d-m-Y', strtotime($invoice['due_date'])) : '—' ?></p>
            <?php
            $statusClass = match($invoice['status'] ?? '') { 'betaald' => 'betaald', 'verstuurd' => 'verstuurd', default => 'concept' };
            ?>
            <span class="status-badge status-<?= $statusClass ?>"><?= htmlspecialchars(strtoupper($invoice['status'] ?? 'CONCEPT')) ?></span>
        </div>
    </div>

    <div class="info-row">
        <div class="info-box">
            <h3>Factureren aan</h3>
            <p><strong><?= htmlspecialchars($customer['name'] ?? '') ?></strong></p>
            <p><?= htmlspecialchars($customer['address'] ?? '') ?></p>
            <p><?= htmlspecialchars($customer['postal'] ?? '') ?> <?= htmlspecialchars($customer['city'] ?? '') ?></p>
            <?php if (!empty($customer['btw_nr'])): ?>
                <p>BTW: <?= htmlspecialchars($customer['btw_nr']) ?></p>
            <?php endif; ?>
        </div>
        <div class="info-box" style="text-align:right;">
            <h3>Betalingsgegevens</h3>
            <p>Vervaldatum: <strong><?= $invoice['due_date'] ? date('d-m-Y', strtotime($invoice['due_date'])) : '—' ?></strong></p>
            <p>Referentie: <?= htmlspecialchars($invoice['number']) ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Beschrijving</th>
                <th class="text-right">Aantal</th>
                <th class="text-right">Prijs p/st</th>
                <th class="text-right">BTW</th>
                <th class="text-right">Totaal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['description']) ?></td>
                <td class="text-right"><?= number_format((float)$item['quantity'], 2, ',', '.') ?></td>
                <td class="text-right">€ <?= number_format((float)$item['unit_price'], 2, ',', '.') ?></td>
                <td class="text-right"><?= number_format((float)$item['btw_rate'], 0) ?>%</td>
                <td class="text-right">€ <?= number_format((float)$item['total'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotaal:</td>
                <td class="text-right">€ <?= number_format((float)$invoice['subtotal'], 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td>BTW (<?= number_format((float)$invoice['btw_rate'], 0) ?>%):</td>
                <td class="text-right">€ <?= number_format((float)$invoice['btw_amount'], 2, ',', '.') ?></td>
            </tr>
            <tr class="total-row">
                <td>Totaal:</td>
                <td class="text-right">€ <?= number_format((float)$invoice['total'], 2, ',', '.') ?></td>
            </tr>
        </table>
    </div>

    <?php if (!empty($invoice['notes'])): ?>
    <div style="margin-top:30px;">
        <h3 style="font-size:10px;text-transform:uppercase;color:#999;margin:0 0 5px;">Opmerkingen</h3>
        <p style="font-size:10px;color:#666;"><?= nl2br(htmlspecialchars($invoice['notes'])) ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p><?= htmlspecialchars($company['name']) ?> · <?= htmlspecialchars($company['address'] ?? '') ?> · KvK <?= htmlspecialchars($company['kvk'] ?? '') ?> · BTW <?= htmlspecialchars($company['btw_nr'] ?? '') ?></p>
    </div>
</body>
</html>
