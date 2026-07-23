<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Magazijnen', 'activeModule' => 'voorraad']);
require __DIR__ . '/../_nav.php';
voorraadNav('warehouses');
?>
<div class="hz-grid" style="grid-template-columns:1.5fr 1fr;gap:1.5rem;align-items:start;">
    <div class="hz-card">
        <div class="hz-card__header"><h2 style="margin:0;font-size:1rem;">Magazijnen</h2></div>
        <table class="hz-table">
            <thead><tr><th>Naam</th><th>Locatie</th><th>Totale voorraad</th></tr></thead>
            <tbody>
                <?php if (empty($warehouses)): ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">Nog geen magazijnen</td></tr>
                <?php else: foreach ($warehouses as $w): ?>
                    <tr>
                        <td><?= htmlspecialchars($w['name']) ?></td>
                        <td><?= htmlspecialchars((string) $w['location']) ?></td>
                        <td><?= (int) $w['total_qty'] ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div class="hz-card">
        <h2 style="margin:0 0 .75rem;font-size:1rem;">Nieuw magazijn</h2>
        <form method="post" action="<?= BASE ?>/voorraad/magazijnen"><?= \Core\Csrf::field() ?>
            <div class="hz-field"><input type="text" name="name" placeholder=" " required><label>Naam</label></div>
            <div class="hz-field"><input type="text" name="location" placeholder=" "><label>Locatie / adres</label></div>
            <button type="submit" class="hz-btn hz-btn--primary">Toevoegen</button>
        </form>
    </div>
</div>
<?php View::partial('footer'); ?>
