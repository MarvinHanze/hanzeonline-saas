<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => $lead['name'], 'activeModule' => 'crm']);
require __DIR__ . '/../_nav.php';
crmNav('leads');
?>
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;">
    <div>
        <h1 style="font-size:1.2rem;margin:0 0 .2rem;"><?= htmlspecialchars($lead['name']) ?></h1>
        <p style="color:var(--hz-text-muted);margin:0;"><?= htmlspecialchars((string) $lead['company']) ?></p>
    </div>
    <a href="<?= BASE ?>/crm/leads/<?= (int) $lead['id'] ?>/bewerk" class="hz-btn hz-btn--secondary">Bewerken</a>
</div>

<div class="hz-grid hz-grid--3" style="margin-bottom:1.5rem;">
    <div class="hz-card">
        <p style="color:var(--hz-text-muted);font-size:.8rem;margin:0 0 .3rem;">Status</p>
        <span class="hz-badge hz-badge--gray"><?= htmlspecialchars($lead['status']) ?></span>
    </div>
    <div class="hz-card">
        <p style="color:var(--hz-text-muted);font-size:.8rem;margin:0 0 .3rem;">Geschatte waarde</p>
        <p style="margin:0;font-weight:600;">€ <?= number_format((float) $lead['value'], 2, ',', '.') ?></p>
    </div>
    <div class="hz-card">
        <p style="color:var(--hz-text-muted);font-size:.8rem;margin:0 0 .3rem;">Contact</p>
        <p style="margin:0;"><?= htmlspecialchars((string) $lead['email']) ?><br><?= htmlspecialchars((string) $lead['phone']) ?></p>
    </div>
</div>

<?php if (!empty($lead['notes'])): ?>
    <div class="hz-card" style="margin-bottom:1.5rem;">
        <p style="color:var(--hz-text-muted);font-size:.8rem;margin:0 0 .3rem;">Notities</p>
        <p style="margin:0;white-space:pre-wrap;"><?= htmlspecialchars($lead['notes']) ?></p>
    </div>
<?php endif; ?>

<div class="hz-card">
    <div class="hz-card__header">
        <h2 style="margin:0;font-size:1rem;">Offertes</h2>
        <a href="<?= BASE ?>/crm/offertes/nieuw" class="hz-btn hz-btn--ghost">Nieuwe offerte</a>
    </div>
    <table class="hz-table">
        <thead><tr><th>Nummer</th><th>Titel</th><th>Status</th><th>Bedrag</th></tr></thead>
        <tbody>
            <?php if (empty($quotes)): ?>
                <tr><td colspan="4" style="text-align:center;color:var(--hz-text-muted);padding:1.25rem;">Nog geen offertes voor deze lead</td></tr>
            <?php else: foreach ($quotes as $q): ?>
                <tr>
                    <td><?= htmlspecialchars($q['number']) ?></td>
                    <td><?= htmlspecialchars($q['title']) ?></td>
                    <td><span class="hz-badge hz-badge--gray"><?= htmlspecialchars($q['status']) ?></span></td>
                    <td>€ <?= number_format((float) $q['amount'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('footer'); ?>
