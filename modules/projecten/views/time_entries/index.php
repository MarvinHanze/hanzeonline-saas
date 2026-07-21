<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Mijn uren', 'activeModule' => 'projecten']);
require __DIR__ . '/../_nav.php';
projectenNav('uren');
?>
<div class="hz-card hz-card--stat" style="max-width:260px;margin-bottom:1.5rem;">
    <p class="hz-card__label">Deze week geboekt</p>
    <p class="hz-card__value"><?= number_format($totalThisWeek, 1, ',', '.') ?> uur</p>
</div>

<div class="hz-card">
    <div class="hz-card__header"><h2 style="margin:0;font-size:1rem;">Mijn recente urenregistraties</h2></div>
    <table class="hz-table">
        <thead><tr><th>Datum</th><th>Project</th><th>Uren</th><th>Omschrijving</th><th>Factureerbaar</th></tr></thead>
        <tbody>
            <?php if (empty($entries)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">
                    Nog geen uren geboekt. Ga naar een project om uren te loggen.
                </td></tr>
            <?php else: foreach ($entries as $e): ?>
                <tr>
                    <td><?= htmlspecialchars($e['entry_date']) ?></td>
                    <td><a href="<?= BASE ?>/projecten/projecten/<?= (int) $e['project_id'] ?>" style="color:var(--hz-primary);text-decoration:none;"><?= htmlspecialchars($e['project_name']) ?></a></td>
                    <td><?= number_format((float) $e['hours'], 1, ',', '.') ?></td>
                    <td><?= htmlspecialchars((string) $e['description']) ?></td>
                    <td><?= $e['billable'] ? '<span class="hz-badge hz-badge--green">Ja</span>' : '<span class="hz-badge hz-badge--gray">Nee</span>' ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('footer'); ?>
