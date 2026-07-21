<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Nieuwe lead', 'activeModule' => 'crm']);
require __DIR__ . '/../_nav.php';
crmNav('leads');
?>
<div class="hz-card" style="max-width:560px;">
    <h1 style="font-size:1.1rem;margin:0 0 1rem;">Nieuwe lead</h1>
    <form method="post" action="<?= BASE ?>/crm/leads">
        <div class="hz-field"><input type="text" name="name" placeholder=" " required><label>Naam</label></div>
        <div class="hz-field"><input type="text" name="company" placeholder=" "><label>Bedrijf</label></div>
        <div class="hz-field"><input type="email" name="email" placeholder=" "><label>E-mailadres</label></div>
        <div class="hz-field"><input type="text" name="phone" placeholder=" "><label>Telefoon</label></div>
        <div class="hz-field"><input type="text" name="source" placeholder=" "><label>Bron (bv. website, beurs, referral)</label></div>
        <div class="hz-field"><input type="number" name="value" step="0.01" min="0" placeholder=" "><label>Geschatte waarde (€)</label></div>
        <div style="margin-bottom:1.1rem;">
            <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.35rem;">Status</label>
            <select name="status">
                <?php foreach ($statuses as $s): ?>
                    <option value="<?= $s ?>"><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="hz-field"><textarea name="notes" placeholder=" " rows="3"></textarea><label>Notities</label></div>
        <button type="submit" class="hz-btn hz-btn--primary">Opslaan</button>
        <a href="<?= BASE ?>/crm/leads" class="hz-btn hz-btn--ghost">Annuleren</a>
    </form>
</div>
<?php View::partial('footer'); ?>
