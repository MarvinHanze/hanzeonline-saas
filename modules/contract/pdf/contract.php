<!DOCTYPE html>
<html>
<head>
    <base href="/saas-platform/">
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #f59e0b; padding-bottom: 20px; }
        .header h1 { font-size: 20px; color: #1e293b; margin: 0 0 5px; }
        .header p { color: #64748b; font-size: 10px; margin: 2px 0; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 25px; padding: 15px; background: #f8fafc; border-radius: 8px; }
        .meta-box h3 { font-size: 9px; text-transform: uppercase; color: #94a3b8; margin: 0 0 5px; letter-spacing: 0.05em; }
        .meta-box p { margin: 2px 0; font-size: 10px; }
        .content { margin-bottom: 30px; }
        .content h2 { font-size: 14px; color: #1e293b; margin: 0 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        .content p { margin: 8px 0; font-size: 11px; }
        .signature-section { margin-top: 40px; page-break-inside: avoid; }
        .signature-box { display: flex; justify-content: space-between; gap: 40px; margin-top: 20px; }
        .sig-area { width: 250px; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px; text-align: center; }
        .sig-area p { font-size: 9px; color: #94a3b8; margin-top: 5px; }
        .footer { margin-top: 50px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #999; text-align: center; }
        .watermark { position: fixed; top: 40%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 60px; color: rgba(0,0,0,0.03); font-weight: bold; text-transform: uppercase; z-index: -1; }
    </style>
</head>
<body>
    <div class="watermark"><?= htmlspecialchars($contract['status'] ?? 'CONCEPT') ?></div>

    <div class="header">
        <h1><?= htmlspecialchars($contract['title'] ?? 'CONTRACT') ?></h1>
        <p><?= htmlspecialchars($company['name']) ?></p>
        <p><?= htmlspecialchars($company['address'] ?? '') ?></p>
    </div>

    <div class="meta">
        <div class="meta-box">
            <h3>Contract</h3>
            <p><strong>#<?= $contract['id'] ?? '' ?></strong></p>
            <p>Status: <?= htmlspecialchars(strtoupper($contract['status'] ?? 'concept')) ?></p>
        </div>
        <div class="meta-box">
            <h3>Partijen</h3>
            <p><strong><?= htmlspecialchars($company['name']) ?></strong></p>
            <?php if (!empty($contract['customer_name'])): ?>
                <p><strong><?= htmlspecialchars($contract['customer_name']) ?></strong></p>
            <?php endif; ?>
        </div>
        <div class="meta-box">
            <h3>Looptijd</h3>
            <p>Van: <?= $contract['start_date'] ? date('d-m-Y', strtotime($contract['start_date'])) : '—' ?></p>
            <p>Tot: <?= $contract['end_date'] ? date('d-m-Y', strtotime($contract['end_date'])) : '—' ?></p>
        </div>
    </div>

    <div class="content">
        <?= $contract['content_html'] ?? '' ?>
    </div>

    <?php if (!empty($contract['signed_at'])): ?>
    <div class="signature-section">
        <h2 style="font-size:14px;border-bottom:1px solid #e2e8f0;padding-bottom:5px;">Handtekeningen</h2>
        <div class="signature-box">
            <div>
                <?php if (!empty($contract['signature_data'])): ?>
                    <img src="<?= htmlspecialchars($contract['signature_data']) ?>" style="max-height:60px;">
                <?php else: ?>
                    <div class="sig-area"></div>
                <?php endif; ?>
                <p style="font-size:9px;color:#94a3b8;">Ondertekend door: <?= htmlspecialchars($contract['signed_by'] ?? '') ?></p>
                <p style="font-size:9px;color:#94a3b8;">Datum: <?= $contract['signed_at'] ? date('d-m-Y H:i', strtotime($contract['signed_at'])) : '' ?></p>
            </div>
            <div>
                <div class="sig-area"></div>
                <p style="font-size:9px;color:#94a3b8;"> <?= htmlspecialchars($company['name']) ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p><?= htmlspecialchars($company['name']) ?> · <?= htmlspecialchars($company['address'] ?? '') ?> · KvK <?= htmlspecialchars($company['kvk'] ?? '') ?></p>
        <p>Dit document is gegenereerd op <?= date('d-m-Y H:i') ?></p>
    </div>
</body>
</html>
