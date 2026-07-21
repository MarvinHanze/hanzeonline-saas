<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Producten', 'activeModule' => 'voorraad']);
require __DIR__ . '/../_nav.php';
voorraadNav('products');
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
    <form method="get" style="display:flex;gap:.5rem;">
        <input type="text" name="search" placeholder="Zoeken op naam/SKU" value="<?= htmlspecialchars($search) ?>" style="padding:.5rem .75rem;border:1px solid var(--hz-border);border-radius:var(--hz-radius);">
        <button type="submit" class="hz-btn hz-btn--secondary">Zoeken</button>
    </form>
    <a href="<?= BASE ?>/voorraad/producten/nieuw" class="hz-btn hz-btn--primary"><?= hz_icon('plus') ?> Nieuw product</a>
</div>

<div class="hz-card">
    <table class="hz-table" data-hz-sortable>
        <thead>
            <tr>
                <th data-key="sku">SKU</th>
                <th data-key="name">Naam</th>
                <th data-key="total_qty">Voorraad</th>
                <th data-key="sales_price">Verkoopprijs</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="4" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">Geen producten gevonden</td></tr>
            <?php else: foreach ($products as $p): ?>
                <tr data-row>
                    <td data-col="sku"><?= htmlspecialchars($p['sku']) ?></td>
                    <td data-col="name"><a href="<?= BASE ?>/voorraad/producten/<?= (int) $p['id'] ?>" style="color:var(--hz-primary);text-decoration:none;"><?= htmlspecialchars($p['name']) ?></a></td>
                    <td data-col="total_qty">
                        <?php if ((int) $p['total_qty'] <= (int) $p['min_stock']): ?>
                            <span class="hz-badge hz-badge--red"><?= (int) $p['total_qty'] ?> <?= htmlspecialchars($p['unit']) ?></span>
                        <?php else: ?>
                            <?= (int) $p['total_qty'] ?> <?= htmlspecialchars($p['unit']) ?>
                        <?php endif; ?>
                    </td>
                    <td data-col="sales_price">€ <?= number_format((float) $p['sales_price'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('footer'); ?>
