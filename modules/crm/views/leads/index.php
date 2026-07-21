<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Leads', 'activeModule' => 'crm']);
require __DIR__ . '/../_nav.php';
crmNav('leads');
?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
    <form method="get" style="display:flex;gap:.5rem;">
        <input type="text" name="search" placeholder="Zoeken op naam/bedrijf/e-mail" value="<?= htmlspecialchars($search) ?>" style="padding:.5rem .75rem;border:1px solid var(--hz-border);border-radius:var(--hz-radius);">
        <select name="status" onchange="this.form.submit()">
            <option value="">Alle statussen</option>
            <?php foreach ($statuses as $s): ?>
                <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="hz-btn hz-btn--secondary">Filteren</button>
    </form>
    <a href="<?= BASE ?>/crm/leads/nieuw" class="hz-btn hz-btn--primary"><?= hz_icon('plus') ?> Nieuwe lead</a>
</div>

<div class="hz-card">
    <table class="hz-table" data-hz-sortable>
        <thead>
            <tr>
                <th data-key="name">Naam</th>
                <th data-key="company">Bedrijf</th>
                <th data-key="status">Status</th>
                <th data-key="value">Waarde</th>
                <th data-key="source">Bron</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($leads)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">Geen leads gevonden</td></tr>
            <?php else: foreach ($leads as $l): ?>
                <tr data-row>
                    <td data-col="name"><a href="<?= BASE ?>/crm/leads/<?= (int) $l['id'] ?>" style="color:var(--hz-primary);text-decoration:none;"><?= htmlspecialchars($l['name']) ?></a></td>
                    <td data-col="company"><?= htmlspecialchars($l['company']) ?></td>
                    <td data-col="status">
                        <?php
                        $badgeClass = match ($l['status']) {
                            'gewonnen' => 'hz-badge--green',
                            'verloren' => 'hz-badge--red',
                            'offerte' => 'hz-badge--orange',
                            default => 'hz-badge--gray',
                        };
                        ?>
                        <span class="hz-badge <?= $badgeClass ?>"><?= htmlspecialchars($l['status']) ?></span>
                    </td>
                    <td data-col="value">€ <?= number_format((float) $l['value'], 2, ',', '.') ?></td>
                    <td data-col="source"><?= htmlspecialchars($l['source']) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('footer'); ?>
