<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Verkooporders', 'activeModule' => 'crm']);
require __DIR__ . '/../_nav.php';
crmNav('orders');
?>
<div style="display:flex;justify-content:flex-end;margin-bottom:1.25rem;">
    <a href="<?= BASE ?>/crm/orders/nieuw" class="hz-btn hz-btn--primary"><?= hz_icon('plus') ?> Nieuwe order</a>
</div>

<div class="hz-card">
    <table class="hz-table">
        <thead><tr><th>Nummer</th><th>Klant</th><th>Offerte</th><th>Bedrag</th><th>Datum</th><th>Status</th></tr></thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="6" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">Nog geen verkooporders</td></tr>
            <?php else: foreach ($orders as $o): ?>
                <tr>
                    <td><?= htmlspecialchars($o['number']) ?></td>
                    <td><?= htmlspecialchars($o['customer_name']) ?></td>
                    <td><?= htmlspecialchars((string) $o['quote_number']) ?: '—' ?></td>
                    <td>€ <?= number_format((float) $o['amount'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars((string) $o['order_date']) ?></td>
                    <td>
                        <form method="post" action="<?= BASE ?>/crm/orders/<?= (int) $o['id'] ?>/status" style="display:inline;"><?= \Core\Csrf::field() ?>
                            <select name="status" onchange="this.form.submit()">
                                <?php foreach ($statuses as $s): ?>
                                    <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('footer'); ?>
