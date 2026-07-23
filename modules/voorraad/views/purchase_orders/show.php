<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => $order['number'], 'activeModule' => 'voorraad']);
require __DIR__ . '/../_nav.php';
voorraadNav('purchase_orders');
?>
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;">
    <div>
        <h1 style="font-size:1.2rem;margin:0 0 .2rem;"><?= htmlspecialchars($order['number']) ?></h1>
        <p style="color:var(--hz-text-muted);margin:0;"><?= htmlspecialchars($order['supplier_name']) ?></p>
    </div>
    <form method="post" action="<?= BASE ?>/voorraad/inkooporders/<?= (int) $order['id'] ?>/status"><?= \Core\Csrf::field() ?>
        <select name="status" onchange="this.form.submit()">
            <?php foreach ($statuses as $s): ?>
                <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="hz-card" style="margin-bottom:1.5rem;">
    <table class="hz-table">
        <thead><tr><th>Product</th><th>Aantal</th><th>Prijs</th><th>Totaal</th></tr></thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="4" style="text-align:center;color:var(--hz-text-muted);padding:1.25rem;">Geen orderregels</td></tr>
            <?php else: foreach ($items as $i): ?>
                <tr>
                    <td><?= htmlspecialchars($i['sku']) ?> — <?= htmlspecialchars($i['product_name']) ?></td>
                    <td><?= (int) $i['quantity'] ?></td>
                    <td>€ <?= number_format((float) $i['unit_price'], 2, ',', '.') ?></td>
                    <td>€ <?= number_format((float) $i['total'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<?php if ($order['status'] === 'ontvangen'): ?>
    <div class="hz-card" style="border-left:4px solid var(--hz-success);">
        Deze order is ontvangen — de voorraad is bijgewerkt in het gekoppelde magazijn.
    </div>
<?php endif; ?>
<?php View::partial('footer'); ?>
