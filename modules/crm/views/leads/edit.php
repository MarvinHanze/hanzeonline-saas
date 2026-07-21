<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Lead bewerken', 'activeModule' => 'crm']);
require __DIR__ . '/../_nav.php';
crmNav('leads');
?>
<div class="hz-card" style="max-width:560px;">
    <h1 style="font-size:1.1rem;margin:0 0 1rem;">Lead bewerken</h1>
    <form method="post" action="<?= BASE ?>/crm/leads/<?= (int) $lead['id'] ?>">
        <div class="hz-field"><input type="text" name="name" placeholder=" " required value="<?= htmlspecialchars($lead['name']) ?>"><label>Naam</label></div>
        <div class="hz-field"><input type="text" name="company" placeholder=" " value="<?= htmlspecialchars((string) $lead['company']) ?>"><label>Bedrijf</label></div>
        <div class="hz-field"><input type="email" name="email" placeholder=" " value="<?= htmlspecialchars((string) $lead['email']) ?>"><label>E-mailadres</label></div>
        <div class="hz-field"><input type="text" name="phone" placeholder=" " value="<?= htmlspecialchars((string) $lead['phone']) ?>"><label>Telefoon</label></div>
        <div class="hz-field"><input type="text" name="source" placeholder=" " value="<?= htmlspecialchars((string) $lead['source']) ?>"><label>Bron</label></div>
        <div class="hz-field"><input type="number" name="value" step="0.01" min="0" placeholder=" " value="<?= htmlspecialchars((string) $lead['value']) ?>"><label>Geschatte waarde (€)</label></div>
        <div style="margin-bottom:1.1rem;">
            <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.35rem;">Status</label>
            <select name="status">
                <?php foreach ($statuses as $s): ?>
                    <option value="<?= $s ?>" <?= $lead['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="hz-field"><textarea name="notes" placeholder=" " rows="3"><?= htmlspecialchars((string) $lead['notes']) ?></textarea><label>Notities</label></div>
        <button type="submit" class="hz-btn hz-btn--primary">Opslaan</button>
        <a href="<?= BASE ?>/crm/leads/<?= (int) $lead['id'] ?>" class="hz-btn hz-btn--ghost">Annuleren</a>
    </form>
</div>
<?php View::partial('footer'); ?>
