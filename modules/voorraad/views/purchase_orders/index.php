<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Inkooporders', 'activeModule' => 'voorraad']);
require __DIR__ . '/../_nav.php';
voorraadNav('purchase_orders');
?>
<div style="display:flex;justify-content:flex-end;margin-bottom:1.25rem;">
    <a href="<?= BASE ?>/voorraad/inkooporders/nieuw" class="hz-btn hz-btn--primary"><?= hz_icon('plus') ?> Nieuwe inkooporder</a>
</div>

<div class="hz-card">
    <table class="hz-table">
        <thead><tr><th>Nummer</th><th>Leverancier</th><th>Magazijn</th><th>Bedrag</th><th>Verwacht</th><th>Status</th></tr></thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="6" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">Nog geen inkooporders</td></tr>
            <?php else: foreach ($orders as $o): ?>
                <tr>
                    <td><a href="<?= BASE ?>/voorraad/inkooporders/<?= (int) $o['id'] ?>" style="color:var(--hz-primary);text-decoration:none;"><?= htmlspecialchars($o['number']) ?></a></td>
                    <td><?= htmlspecialchars($o['supplier_name']) ?></td>
                    <td><?= htmlspecialchars((string) $o['warehouse_name']) ?: '—' ?></td>
                    <td>€ <?= number_format((float) $o['total'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars((string) $o['expected_date']) ?: '—' ?></td>
                    <td>
                        <?php
                        $badgeClass = match ($o['status']) {
                            'ontvangen' => 'hz-badge--green',
                            'geannuleerd' => 'hz-badge--red',
                            'besteld' => 'hz-badge--orange',
                            default => 'hz-badge--gray',
                        };
                        ?>
                        <span class="hz-badge <?= $badgeClass ?>"><?= htmlspecialchars($o['status']) ?></span>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('footer'); ?>
