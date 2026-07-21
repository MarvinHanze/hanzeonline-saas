<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Projecten', 'activeModule' => 'projecten']);
require __DIR__ . '/../_nav.php';
projectenNav('projects');
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
    <form method="get" style="display:flex;gap:.5rem;">
        <select name="status" onchange="this.form.submit()">
            <option value="">Alle statussen</option>
            <?php foreach ($statuses as $s): ?>
                <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    <a href="<?= BASE ?>/projecten/projecten/nieuw" class="hz-btn hz-btn--primary"><?= hz_icon('plus') ?> Nieuw project</a>
</div>

<div class="hz-card">
    <table class="hz-table">
        <thead><tr><th>Naam</th><th>Klant</th><th>Status</th><th>Budget (uren)</th><th>Budget (€)</th></tr></thead>
        <tbody>
            <?php if (empty($projects)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">Geen projecten gevonden</td></tr>
            <?php else: foreach ($projects as $p): ?>
                <tr>
                    <td><a href="<?= BASE ?>/projecten/projecten/<?= (int) $p['id'] ?>" style="color:var(--hz-primary);text-decoration:none;"><?= htmlspecialchars($p['name']) ?></a></td>
                    <td><?= htmlspecialchars((string) $p['client_name']) ?></td>
                    <td>
                        <?php
                        $badgeClass = match ($p['status']) {
                            'actief' => 'hz-badge--green',
                            'geannuleerd' => 'hz-badge--red',
                            'on_hold' => 'hz-badge--orange',
                            default => 'hz-badge--gray',
                        };
                        ?>
                        <span class="hz-badge <?= $badgeClass ?>"><?= htmlspecialchars(str_replace('_', ' ', $p['status'])) ?></span>
                    </td>
                    <td><?= number_format((float) $p['budget_hours'], 1, ',', '.') ?></td>
                    <td>€ <?= number_format((float) $p['budget_amount'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('footer'); ?>
