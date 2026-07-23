<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Nieuw product', 'activeModule' => 'voorraad']);
require __DIR__ . '/../_nav.php';
voorraadNav('products');
?>
<div class="hz-card" style="max-width:560px;">
    <h1 style="font-size:1.1rem;margin:0 0 1rem;">Nieuw product</h1>
    <form method="post" action="<?= BASE ?>/voorraad/producten"><?= \Core\Csrf::field() ?>
        <div class="hz-field"><input type="text" name="sku" placeholder=" " required><label>SKU / artikelcode</label></div>
        <div class="hz-field"><input type="text" name="name" placeholder=" " required><label>Naam</label></div>
        <div class="hz-field"><input type="text" name="unit" placeholder=" " value="stuks"><label>Eenheid</label></div>
        <div style="display:flex;gap:1rem;">
            <div class="hz-field" style="flex:1;"><input type="number" name="purchase_price" step="0.01" min="0" placeholder=" "><label>Inkoopprijs (€)</label></div>
            <div class="hz-field" style="flex:1;"><input type="number" name="sales_price" step="0.01" min="0" placeholder=" "><label>Verkoopprijs (€)</label></div>
        </div>
        <div class="hz-field"><input type="number" name="min_stock" min="0" placeholder=" " value="0"><label>Minimale voorraad</label></div>
        <button type="submit" class="hz-btn hz-btn--primary">Opslaan</button>
        <a href="<?= BASE ?>/voorraad/producten" class="hz-btn hz-btn--ghost">Annuleren</a>
    </form>
</div>
<?php View::partial('footer'); ?>
