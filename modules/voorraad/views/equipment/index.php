<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Materieel', 'activeModule' => 'voorraad']);
require __DIR__ . '/../_nav.php';
voorraadNav('equipment');
?>
<div class="hz-grid" style="grid-template-columns:1.6fr 1fr;gap:1.5rem;align-items:start;">
    <div class="hz-card">
        <div class="hz-card__header"><h2 style="margin:0;font-size:1rem;">Materieel</h2></div>
        <table class="hz-table">
            <thead><tr><th>Naam</th><th>Categorie</th><th>Serienummer</th><th>Status</th></tr></thead>
            <tbody>
                <?php if (empty($equipment)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">Nog geen materieel geregistreerd</td></tr>
                <?php else: foreach ($equipment as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['name']) ?><?= $e['assigned_to'] ? ' <span style="color:var(--hz-text-muted);font-size:.8rem;">(' . htmlspecialchars($e['assigned_to']) . ')</span>' : '' ?></td>
                        <td><?= htmlspecialchars((string) $e['category']) ?></td>
                        <td><?= htmlspecialchars((string) $e['serial_number']) ?></td>
                        <td>
                            <form method="post" action="<?= BASE ?>/voorraad/materieel/<?= (int) $e['id'] ?>/status" style="display:flex;gap:.4rem;align-items:center;">
                                <select name="status">
                                    <?php foreach ($statuses as $s): ?>
                                        <option value="<?= $s ?>" <?= $e['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="assigned_to" placeholder="Bij wie" value="<?= htmlspecialchars((string) $e['assigned_to']) ?>" style="width:90px;padding:.35rem;border:1px solid var(--hz-border);border-radius:var(--hz-radius);">
                                <button type="submit" class="hz-btn hz-btn--ghost">OK</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div class="hz-card">
        <h2 style="margin:0 0 .75rem;font-size:1rem;">Materieel toevoegen</h2>
        <form method="post" action="<?= BASE ?>/voorraad/materieel">
            <div class="hz-field"><input type="text" name="name" placeholder=" " required><label>Naam</label></div>
            <div class="hz-field"><input type="text" name="category" placeholder=" "><label>Categorie</label></div>
            <div class="hz-field"><input type="text" name="serial_number" placeholder=" "><label>Serienummer</label></div>
            <div class="hz-field"><input type="text" name="location" placeholder=" "><label>Locatie</label></div>
            <div class="hz-field"><input type="date" name="purchase_date" placeholder=" "><label>Aanschafdatum</label></div>
            <button type="submit" class="hz-btn hz-btn--primary">Toevoegen</button>
        </form>
    </div>
</div>
<?php View::partial('footer'); ?>
