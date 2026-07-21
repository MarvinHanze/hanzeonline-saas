<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Voorraad', 'activeModule' => 'voorraad']);
require __DIR__ . '/../_nav.php';
voorraadNav('dashboard');
?>
<div class="hz-grid hz-grid--3" style="margin-bottom:2rem;">
    <div class="hz-card hz-card--stat">
        <p class="hz-card__label">Producten</p>
        <p class="hz-card__value"><?= $totalProducts ?></p>
    </div>
    <div class="hz-card hz-card--stat">
        <p class="hz-card__label">Openstaande inkooporders</p>
        <p class="hz-card__value"><?= $openPurchaseOrders ?></p>
    </div>
    <div class="hz-card hz-card--stat">
        <p class="hz-card__label">Materieel in gebruik</p>
        <p class="hz-card__value"><?= $equipmentInUse ?> / <?= $equipmentTotal ?></p>
    </div>
</div>

<div class="hz-card">
    <div class="hz-card__header">
        <h2 style="margin:0;font-size:1rem;">Lage voorraad</h2>
        <a href="<?= BASE ?>/voorraad/producten" class="hz-btn hz-btn--ghost">Alle producten</a>
    </div>
    <table class="hz-table">
        <thead><tr><th>SKU</th><th>Product</th><th>Voorraad</th><th>Minimum</th></tr></thead>
        <tbody>
            <?php if (empty($lowStock)): ?>
                <tr><td colspan="4" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">Geen producten onder het minimum</td></tr>
            <?php else: foreach ($lowStock as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['sku']) ?></td>
                    <td><a href="<?= BASE ?>/voorraad/producten/<?= (int) $p['id'] ?>" style="color:var(--hz-primary);text-decoration:none;"><?= htmlspecialchars($p['name']) ?></a></td>
                    <td><span class="hz-badge hz-badge--red"><?= (int) $p['total_qty'] ?></span></td>
                    <td><?= (int) $p['min_stock'] ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('footer'); ?>
