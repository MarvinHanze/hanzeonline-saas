<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Nieuwe inkooporder', 'activeModule' => 'voorraad']);
require __DIR__ . '/../_nav.php';
voorraadNav('purchase_orders');
?>
<div class="hz-card" style="max-width:560px;">
    <h1 style="font-size:1.1rem;margin:0 0 1rem;">Nieuwe inkooporder</h1>
    <form method="post" action="<?= BASE ?>/voorraad/inkooporders"><?= \Core\Csrf::field() ?>
        <div class="hz-field"><input type="text" name="supplier_name" placeholder=" " required><label>Leverancier</label></div>

        <div style="margin-bottom:1.1rem;">
            <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.35rem;">Product</label>
            <select name="product_id" onchange="var o=this.options[this.selectedIndex];document.getElementById('unit_price').value=o.dataset.price||'';">
                <option value="0">— Kies product —</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= (int) $p['id'] ?>" data-price="<?= htmlspecialchars((string) $p['purchase_price']) ?>"><?= htmlspecialchars($p['sku']) ?> — <?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display:flex;gap:1rem;">
            <div class="hz-field" style="flex:1;"><input type="number" name="quantity" min="1" value="1" placeholder=" "><label>Aantal</label></div>
            <div class="hz-field" style="flex:1;"><input type="number" name="unit_price" id="unit_price" step="0.01" min="0" placeholder=" "><label>Prijs per stuk (€)</label></div>
        </div>

        <div style="margin-bottom:1.1rem;">
            <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.35rem;">Ontvangst-magazijn</label>
            <select name="warehouse_id">
                <option value="0">— Kies magazijn —</option>
                <?php foreach ($warehouses as $w): ?>
                    <option value="<?= (int) $w['id'] ?>"><?= htmlspecialchars($w['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex;gap:1rem;">
            <div class="hz-field" style="flex:1;"><input type="date" name="order_date" placeholder=" " value="<?= date('Y-m-d') ?>"><label>Besteldatum</label></div>
            <div class="hz-field" style="flex:1;"><input type="date" name="expected_date" placeholder=" "><label>Verwachte levering</label></div>
        </div>
        <div class="hz-field"><textarea name="notes" placeholder=" " rows="3"></textarea><label>Notities</label></div>

        <button type="submit" class="hz-btn hz-btn--primary">Opslaan</button>
        <a href="<?= BASE ?>/voorraad/inkooporders" class="hz-btn hz-btn--ghost">Annuleren</a>
    </form>
</div>
<?php View::partial('footer'); ?>
