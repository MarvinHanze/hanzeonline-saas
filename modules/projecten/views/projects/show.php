<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => $project['name'], 'activeModule' => 'projecten']);
require __DIR__ . '/../_nav.php';
projectenNav('projects');
?>
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;">
    <div>
        <h1 style="font-size:1.2rem;margin:0 0 .2rem;"><?= htmlspecialchars($project['name']) ?></h1>
        <p style="color:var(--hz-text-muted);margin:0;"><?= htmlspecialchars((string) $project['client_name']) ?></p>
    </div>
    <a href="<?= BASE ?>/projecten/projecten/<?= (int) $project['id'] ?>/bewerk" class="hz-btn hz-btn--secondary">Bewerken</a>
</div>

<div class="hz-grid hz-grid--3" style="margin-bottom:1.5rem;">
    <div class="hz-card">
        <p style="color:var(--hz-text-muted);font-size:.8rem;margin:0 0 .3rem;">Status</p>
        <span class="hz-badge hz-badge--gray"><?= htmlspecialchars(str_replace('_', ' ', $project['status'])) ?></span>
    </div>
    <div class="hz-card">
        <p style="color:var(--hz-text-muted);font-size:.8rem;margin:0 0 .3rem;">Periode</p>
        <p style="margin:0;"><?= htmlspecialchars((string) $project['start_date']) ?: '—' ?> t/m <?= htmlspecialchars((string) $project['end_date']) ?: '—' ?></p>
    </div>
    <div class="hz-card">
        <p style="color:var(--hz-text-muted);font-size:.8rem;margin:0 0 .3rem;">Uren besteed / budget</p>
        <p style="margin:0;font-weight:600;"><?= number_format($totalHours, 1, ',', '.') ?> / <?= number_format((float) $project['budget_hours'], 1, ',', '.') ?> uur</p>
    </div>
</div>

<div class="hz-grid" style="grid-template-columns:1.3fr 1fr;gap:1.5rem;align-items:start;">
    <div class="hz-card">
        <div class="hz-card__header"><h2 style="margin:0;font-size:1rem;">Taken</h2></div>
        <table class="hz-table" style="margin-bottom:1rem;">
            <thead><tr><th>Taak</th><th>Toegewezen aan</th><th>Deadline</th><th>Status</th></tr></thead>
            <tbody>
                <?php if (empty($tasks)): ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--hz-text-muted);padding:1.25rem;">Nog geen taken</td></tr>
                <?php else: foreach ($tasks as $t): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['title']) ?></td>
                        <td><?= htmlspecialchars((string) $t['assignee_name']) ?: '—' ?></td>
                        <td><?= htmlspecialchars((string) $t['due_date']) ?: '—' ?></td>
                        <td>
                            <form method="post" action="<?= BASE ?>/projecten/taken/<?= (int) $t['id'] ?>/status">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="open" <?= $t['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                                    <option value="bezig" <?= $t['status'] === 'bezig' ? 'selected' : '' ?>>Bezig</option>
                                    <option value="klaar" <?= $t['status'] === 'klaar' ? 'selected' : '' ?>>Klaar</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>

        <form method="post" action="<?= BASE ?>/projecten/projecten/<?= (int) $project['id'] ?>/taken" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:flex-end;">
            <div class="hz-field" style="margin-bottom:0;flex:2;min-width:160px;"><input type="text" name="title" placeholder=" " required><label>Nieuwe taak</label></div>
            <select name="assignee_id" style="height:44px;">
                <option value="0">Niet toegewezen</option>
                <?php foreach ($teamMembers as $m): ?>
                    <option value="<?= (int) $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="due_date" style="height:44px;padding:0 .5rem;border:1px solid var(--hz-border);border-radius:var(--hz-radius);">
            <button type="submit" class="hz-btn hz-btn--primary">Toevoegen</button>
        </form>
    </div>

    <div class="hz-card">
        <div class="hz-card__header"><h2 style="margin:0;font-size:1rem;">Uren</h2></div>
        <table class="hz-table" style="margin-bottom:1rem;">
            <thead><tr><th>Datum</th><th>Wie</th><th>Uren</th></tr></thead>
            <tbody>
                <?php if (empty($timeEntries)): ?>
                    <tr><td colspan="3" style="text-align:center;color:var(--hz-text-muted);padding:1rem;">Nog geen uren geboekt</td></tr>
                <?php else: foreach ($timeEntries as $te): ?>
                    <tr>
                        <td><?= htmlspecialchars($te['entry_date']) ?></td>
                        <td><?= htmlspecialchars($te['user_name']) ?></td>
                        <td><?= number_format((float) $te['hours'], 1, ',', '.') ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>

        <form method="post" action="<?= BASE ?>/projecten/projecten/<?= (int) $project['id'] ?>/uren">
            <div style="display:flex;gap:.5rem;">
                <input type="date" name="entry_date" value="<?= date('Y-m-d') ?>" style="flex:1;padding:.5rem;border:1px solid var(--hz-border);border-radius:var(--hz-radius);">
                <input type="number" name="hours" step="0.25" min="0.25" placeholder="Uren" required style="width:90px;padding:.5rem;border:1px solid var(--hz-border);border-radius:var(--hz-radius);">
            </div>
            <input type="text" name="description" placeholder="Omschrijving (optioneel)" style="width:100%;margin-top:.5rem;padding:.5rem;border:1px solid var(--hz-border);border-radius:var(--hz-radius);">
            <label style="display:flex;align-items:center;gap:.4rem;margin-top:.5rem;font-size:.85rem;">
                <input type="checkbox" class="hz-checkbox" name="billable" checked> Factureerbaar
            </label>
            <button type="submit" class="hz-btn hz-btn--primary" style="margin-top:.75rem;width:100%;">Uren boeken</button>
        </form>
    </div>
</div>
<?php View::partial('footer'); ?>
