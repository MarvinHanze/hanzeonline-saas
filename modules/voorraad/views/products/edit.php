<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Product bewerken', 'activeModule' => 'voorraad']);
require __DIR__ . '/../_nav.php';
voorraadNav('products');
?>
<div class="hz-card" style="max-width:560px;">
    <h1 style="font-size:1.1rem;margin:0 0 1rem;">Product bewerken</h1>
    <form method="post" action="<?= BASE ?>/voorraad/producten/<?= (int) $product['id'] ?>"><?= \Core\Csrf::field() ?>
        <div class="hz-field"><input type="text" name="sku" placeholder=" " required value="<?= htmlspecialchars($product['sku']) ?>"><label>SKU / artikelcode</label></div>
        <div class="hz-field"><input type="text" name="name" placeholder=" " required value="<?= htmlspecialchars($product['name']) ?>"><label>Naam</label></div>
        <div class="hz-field"><input type="text" name="unit" placeholder=" " value="<?= htmlspecialchars($product['unit']) ?>"><label>Eenheid</label></div>
        <div style="display:flex;gap:1rem;">
            <div class="hz-field" style="flex:1;"><input type="number" name="purchase_price" step="0.01" min="0" placeholder=" " value="<?= htmlspecialchars((string) $product['purchase_price']) ?>"><label>Inkoopprijs (€)</label></div>
            <div class="hz-field" style="flex:1;"><input type="number" name="sales_price" step="0.01" min="0" placeholder=" " value="<?= htmlspecialchars((string) $product['sales_price']) ?>"><label>Verkoopprijs (€)</label></div>
        </div>
        <div class="hz-field"><input type="number" name="min_stock" min="0" placeholder=" " value="<?= htmlspecialchars((string) $product['min_stock']) ?>"><label>Minimale voorraad</label></div>
        <button type="submit" class="hz-btn hz-btn--primary">Opslaan</button>
        <a href="<?= BASE ?>/voorraad/producten/<?= (int) $product['id'] ?>" class="hz-btn hz-btn--ghost">Annuleren</a>
    </form>
</div>
<?php View::partial('footer'); ?>
