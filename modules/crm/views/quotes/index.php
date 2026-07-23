<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Offertes', 'activeModule' => 'crm']);
require __DIR__ . '/../_nav.php';
crmNav('offertes');
?>
<div style="display:flex;justify-content:flex-end;margin-bottom:1.25rem;">
    <a href="<?= BASE ?>/crm/offertes/nieuw" class="hz-btn hz-btn--primary"><?= hz_icon('plus') ?> Nieuwe offerte</a>
</div>

<div class="hz-card">
    <table class="hz-table">
        <thead><tr><th>Nummer</th><th>Titel</th><th>Lead</th><th>Bedrag</th><th>Geldig tot</th><th>Status</th></tr></thead>
        <tbody>
            <?php if (empty($quotes)): ?>
                <tr><td colspan="6" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">Nog geen offertes</td></tr>
            <?php else: foreach ($quotes as $q): ?>
                <tr>
                    <td><?= htmlspecialchars($q['number']) ?></td>
                    <td><?= htmlspecialchars($q['title']) ?></td>
                    <td><?= htmlspecialchars((string) $q['lead_name']) ?></td>
                    <td>€ <?= number_format((float) $q['amount'], 2, ',', '.') ?></td>
                    <td><?= $q['valid_until'] ? htmlspecialchars($q['valid_until']) : '—' ?></td>
                    <td>
                        <form method="post" action="<?= BASE ?>/crm/offertes/<?= (int) $q['id'] ?>/status" style="display:inline;"><?= \Core\Csrf::field() ?>
                            <select name="status" onchange="this.form.submit()">
                                <?php foreach ($statuses as $s): ?>
                                    <option value="<?= $s ?>" <?= $q['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('footer'); ?>
