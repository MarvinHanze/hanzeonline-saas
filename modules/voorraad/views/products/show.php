<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => $product['name'], 'activeModule' => 'voorraad']);
require __DIR__ . '/../_nav.php';
voorraadNav('products');

$totalQty = array_sum(array_column($stockPerWarehouse, 'quantity'));
?>
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;">
    <div>
        <h1 style="font-size:1.2rem;margin:0 0 .2rem;"><?= htmlspecialchars($product['name']) ?></h1>
        <p style="color:var(--hz-text-muted);margin:0;">SKU: <?= htmlspecialchars($product['sku']) ?></p>
    </div>
    <a href="<?= BASE ?>/voorraad/producten/<?= (int) $product['id'] ?>/bewerk" class="hz-btn hz-btn--secondary">Bewerken</a>
</div>

<div class="hz-grid hz-grid--3" style="margin-bottom:1.5rem;">
    <div class="hz-card">
        <p style="color:var(--hz-text-muted);font-size:.8rem;margin:0 0 .3rem;">Totale voorraad</p>
        <p style="margin:0;font-weight:600;font-size:1.3rem;"><?= $totalQty ?> <?= htmlspecialchars($product['unit']) ?></p>
    </div>
    <div class="hz-card">
        <p style="color:var(--hz-text-muted);font-size:.8rem;margin:0 0 .3rem;">Inkoop / verkoopprijs</p>
        <p style="margin:0;">€ <?= number_format((float) $product['purchase_price'], 2, ',', '.') ?> / € <?= number_format((float) $product['sales_price'], 2, ',', '.') ?></p>
    </div>
    <div class="hz-card">
        <p style="color:var(--hz-text-muted);font-size:.8rem;margin:0 0 .3rem;">Minimale voorraad</p>
        <p style="margin:0;"><?= (int) $product['min_stock'] ?> <?= htmlspecialchars($product['unit']) ?></p>
    </div>
</div>

<div class="hz-card">
    <div class="hz-card__header"><h2 style="margin:0;font-size:1rem;">Voorraad per magazijn</h2></div>
    <table class="hz-table" style="margin-bottom:1.25rem;">
        <thead><tr><th>Magazijn</th><th>Aantal</th><th>Mutatie</th></tr></thead>
        <tbody>
            <?php if (empty($stockPerWarehouse)): ?>
                <tr><td colspan="3" style="text-align:center;color:var(--hz-text-muted);padding:1.25rem;">
                    Nog geen magazijnen. <a href="<?= BASE ?>/voorraad/magazijnen" style="color:var(--hz-primary);">Magazijn toevoegen</a>
                </td></tr>
            <?php else: foreach ($stockPerWarehouse as $sw): ?>
                <tr>
                    <td><?= htmlspecialchars($sw['warehouse_name']) ?></td>
                    <td><?= (int) $sw['quantity'] ?></td>
                    <td>
                        <form method="post" action="<?= BASE ?>/voorraad/producten/<?= (int) $product['id'] ?>/voorraad" style="display:flex;gap:.4rem;"><?= \Core\Csrf::field() ?>
                            <input type="hidden" name="warehouse_id" value="<?= (int) $sw['warehouse_id'] ?>">
                            <input type="number" name="delta" placeholder="+10 / -5" required style="width:90px;padding:.4rem;border:1px solid var(--hz-border);border-radius:var(--hz-radius);">
                            <button type="submit" class="hz-btn hz-btn--secondary">Boeken</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('footer'); ?>
