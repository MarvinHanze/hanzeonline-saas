<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Nieuw project', 'activeModule' => 'projecten']);
require __DIR__ . '/../_nav.php';
projectenNav('projects');
?>
<div class="hz-card" style="max-width:560px;">
    <h1 style="font-size:1.1rem;margin:0 0 1rem;">Nieuw project</h1>
    <form method="post" action="<?= BASE ?>/projecten/projecten"><?= \Core\Csrf::field() ?>
        <div class="hz-field"><input type="text" name="name" placeholder=" " required><label>Projectnaam</label></div>
        <div class="hz-field"><input type="text" name="client_name" placeholder=" "><label>Klant</label></div>
        <div style="margin-bottom:1.1rem;">
            <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.35rem;">Status</label>
            <select name="status">
                <?php foreach ($statuses as $s): ?>
                    <option value="<?= $s ?>"><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display:flex;gap:1rem;">
            <div class="hz-field" style="flex:1;"><input type="date" name="start_date" placeholder=" "><label>Startdatum</label></div>
            <div class="hz-field" style="flex:1;"><input type="date" name="end_date" placeholder=" "><label>Einddatum</label></div>
        </div>
        <div style="display:flex;gap:1rem;">
            <div class="hz-field" style="flex:1;"><input type="number" name="budget_hours" step="0.5" min="0" placeholder=" "><label>Budget (uren)</label></div>
            <div class="hz-field" style="flex:1;"><input type="number" name="budget_amount" step="0.01" min="0" placeholder=" "><label>Budget (€)</label></div>
        </div>
        <div class="hz-field"><textarea name="description" placeholder=" " rows="3"></textarea><label>Omschrijving</label></div>
        <button type="submit" class="hz-btn hz-btn--primary">Opslaan</button>
        <a href="<?= BASE ?>/projecten/projecten" class="hz-btn hz-btn--ghost">Annuleren</a>
    </form>
</div>
<?php View::partial('footer'); ?>
