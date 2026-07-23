<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Nieuwe order', 'activeModule' => 'crm']);
require __DIR__ . '/../_nav.php';
crmNav('orders');
?>
<div class="hz-card" style="max-width:560px;">
    <h1 style="font-size:1.1rem;margin:0 0 1rem;">Nieuwe verkooporder</h1>
    <form method="post" action="<?= BASE ?>/crm/orders"><?= \Core\Csrf::field() ?>
        <div style="margin-bottom:1.1rem;">
            <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.35rem;">Gebaseerd op offerte (optioneel)</label>
            <select name="quote_id" onchange="var o=this.options[this.selectedIndex];document.getElementById('amount').value=o.dataset.amount||'';">
                <option value="0">— Geen offerte gekoppeld —</option>
                <?php foreach ($quotes as $q): ?>
                    <option value="<?= (int) $q['id'] ?>" data-amount="<?= htmlspecialchars((string) $q['amount']) ?>"><?= htmlspecialchars($q['number']) ?> — <?= htmlspecialchars($q['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="hz-field"><input type="text" name="customer_name" placeholder=" " required><label>Klantnaam</label></div>
        <div class="hz-field"><input type="number" name="amount" id="amount" step="0.01" min="0" placeholder=" " required><label>Bedrag (€)</label></div>
        <div class="hz-field"><input type="date" name="order_date" placeholder=" " value="<?= date('Y-m-d') ?>"><label>Orderdatum</label></div>
        <div class="hz-field"><textarea name="notes" placeholder=" " rows="3"></textarea><label>Notities</label></div>
        <button type="submit" class="hz-btn hz-btn--primary">Opslaan</button>
        <a href="<?= BASE ?>/crm/orders" class="hz-btn hz-btn--ghost">Annuleren</a>
    </form>
</div>
<?php View::partial('footer'); ?>
