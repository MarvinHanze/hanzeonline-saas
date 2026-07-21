<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'CRM', 'activeModule' => 'crm']);
require __DIR__ . '/../_nav.php';
crmNav('dashboard');
?>
<div class="hz-grid hz-grid--3" style="margin-bottom:2rem;">
    <div class="hz-card hz-card--stat">
        <p class="hz-card__label">Totaal leads</p>
        <p class="hz-card__value"><?= $totalLeads ?></p>
    </div>
    <div class="hz-card hz-card--stat">
        <p class="hz-card__label">Pijplijnwaarde</p>
        <p class="hz-card__value">€ <?= number_format($pipelineValue, 2, ',', '.') ?></p>
    </div>
    <div class="hz-card hz-card--stat">
        <p class="hz-card__label">Offertes uitstaand</p>
        <p class="hz-card__value"><?= $quotesSent ?></p>
    </div>
    <div class="hz-card hz-card--stat">
        <p class="hz-card__label">Orders gewonnen</p>
        <p class="hz-card__value">€ <?= number_format($ordersWonAmount, 2, ',', '.') ?></p>
    </div>
</div>

<div class="hz-card">
    <div class="hz-card__header"><h2 style="margin:0;font-size:1rem;">Recente leads</h2><a href="<?= BASE ?>/crm/leads" class="hz-btn hz-btn--ghost">Alle leads</a></div>
    <table class="hz-table">
        <thead><tr><th>Naam</th><th>Bedrijf</th><th>Status</th><th>Waarde</th></tr></thead>
        <tbody>
            <?php if (empty($recentLeads)): ?>
                <tr><td colspan="4" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">Nog geen leads</td></tr>
            <?php else: foreach ($recentLeads as $l): ?>
                <tr>
                    <td><a href="<?= BASE ?>/crm/leads/<?= (int) $l['id'] ?>" style="color:var(--hz-primary);text-decoration:none;"><?= htmlspecialchars($l['name']) ?></a></td>
                    <td><?= htmlspecialchars($l['company']) ?></td>
                    <td><span class="hz-badge hz-badge--gray"><?= htmlspecialchars($l['status']) ?></span></td>
                    <td>€ <?= number_format((float) $l['value'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('footer'); ?>
