<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Projecten', 'activeModule' => 'projecten']);
require __DIR__ . '/../_nav.php';
projectenNav('dashboard');
?>
<div class="hz-grid hz-grid--3" style="margin-bottom:2rem;">
    <div class="hz-card hz-card--stat">
        <p class="hz-card__label">Actieve projecten</p>
        <p class="hz-card__value"><?= $activeProjects ?></p>
    </div>
    <div class="hz-card hz-card--stat">
        <p class="hz-card__label">Openstaande taken</p>
        <p class="hz-card__value"><?= $openTasks ?></p>
        <?php if ($overdueTasks > 0): ?>
            <p style="margin-top:.3rem;"><span class="hz-badge hz-badge--red"><?= $overdueTasks ?> te laat</span></p>
        <?php endif; ?>
    </div>
    <div class="hz-card hz-card--stat">
        <p class="hz-card__label">Uren deze week</p>
        <p class="hz-card__value"><?= number_format($hoursThisWeek, 1, ',', '.') ?></p>
    </div>
</div>

<div class="hz-card">
    <div class="hz-card__header"><h2 style="margin:0;font-size:1rem;">Recente projecten</h2><a href="<?= BASE ?>/projecten/projecten" class="hz-btn hz-btn--ghost">Alle projecten</a></div>
    <table class="hz-table">
        <thead><tr><th>Naam</th><th>Klant</th><th>Status</th><th>Periode</th></tr></thead>
        <tbody>
            <?php if (empty($recentProjects)): ?>
                <tr><td colspan="4" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">Nog geen projecten</td></tr>
            <?php else: foreach ($recentProjects as $p): ?>
                <tr>
                    <td><a href="<?= BASE ?>/projecten/projecten/<?= (int) $p['id'] ?>" style="color:var(--hz-primary);text-decoration:none;"><?= htmlspecialchars($p['name']) ?></a></td>
                    <td><?= htmlspecialchars((string) $p['client_name']) ?></td>
                    <td><span class="hz-badge hz-badge--gray"><?= htmlspecialchars(str_replace('_', ' ', $p['status'])) ?></span></td>
                    <td><?= htmlspecialchars((string) $p['start_date']) ?> — <?= htmlspecialchars((string) $p['end_date']) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<?php View::partial('footer'); ?>
