<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Nieuwe offerte', 'activeModule' => 'crm']);
require __DIR__ . '/../_nav.php';
crmNav('offertes');
?>
<div class="hz-card" style="max-width:560px;">
    <h1 style="font-size:1.1rem;margin:0 0 1rem;">Nieuwe offerte</h1>
    <form method="post" action="<?= BASE ?>/crm/offertes">
        <div style="margin-bottom:1.1rem;">
            <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.35rem;">Lead (optioneel)</label>
            <select name="lead_id">
                <option value="0">— Geen lead gekoppeld —</option>
                <?php foreach ($leads as $l): ?>
                    <option value="<?= (int) $l['id'] ?>"><?= htmlspecialchars($l['name']) ?><?= $l['company'] ? ' (' . htmlspecialchars($l['company']) . ')' : '' ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="hz-field"><input type="text" name="title" placeholder=" " required><label>Titel</label></div>
        <div class="hz-field"><input type="number" name="amount" step="0.01" min="0" placeholder=" " required><label>Bedrag (€)</label></div>
        <div class="hz-field"><input type="date" name="valid_until" placeholder=" "><label>Geldig tot</label></div>
        <div class="hz-field"><textarea name="notes" placeholder=" " rows="3"></textarea><label>Notities</label></div>
        <button type="submit" class="hz-btn hz-btn--primary">Opslaan als concept</button>
        <a href="<?= BASE ?>/crm/offertes" class="hz-btn hz-btn--ghost">Annuleren</a>
    </form>
</div>
<?php View::partial('footer'); ?>
