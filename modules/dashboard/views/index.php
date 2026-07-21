<?php
declare(strict_types=1);

use Core\Auth;
use Core\View;

$user = Auth::user();

View::partial('header', ['pageTitle' => 'Dashboard', 'activeModule' => 'dashboard']);
?>
<h1 style="margin:0 0 .25rem;font-size:1.4rem;">Welkom terug, <?= htmlspecialchars($user['name']) ?></h1>
<p style="color:var(--hz-text-muted);margin:0 0 1.5rem;">Hier is een overzicht van je bedrijf.</p>

<div class="hz-grid hz-grid--3" style="margin-bottom:2rem;">
    <?php if ($revenue !== null): ?>
        <div class="hz-card hz-card--stat">
            <p class="hz-card__label">Omzet deze maand</p>
            <p class="hz-card__value">€ <?= number_format($revenue['month_amount'], 2, ',', '.') ?></p>
            <p style="color:var(--hz-text-muted);font-size:.8rem;margin-top:.25rem;">
                <?= $revenue['month_count'] ?> betaalde facturen &middot; totaal € <?= number_format($revenue['total_amount'], 2, ',', '.') ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if ($outstanding !== null): ?>
        <div class="hz-card hz-card--stat">
            <p class="hz-card__label">Openstaande facturen</p>
            <p class="hz-card__value">€ <?= number_format($outstanding['amount'], 2, ',', '.') ?></p>
            <p style="font-size:.8rem;margin-top:.25rem;">
                <span class="hz-badge hz-badge--gray"><?= $outstanding['count'] ?> openstaand</span>
                <?php if ($outstanding['overdue_count'] > 0): ?>
                    <span class="hz-badge hz-badge--red"><?= $outstanding['overdue_count'] ?> achterstallig</span>
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if ($projectStatus !== null): ?>
        <div class="hz-card hz-card--stat">
            <p class="hz-card__label">Projecten</p>
            <p class="hz-card__value"><?= $projectStatus['actief'] ?> actief</p>
            <p style="font-size:.8rem;margin-top:.25rem;">
                <span class="hz-badge hz-badge--gray"><?= $projectStatus['gepland'] ?> gepland</span>
                <span class="hz-badge hz-badge--green"><?= $projectStatus['afgerond'] ?> afgerond</span>
                <?php if ($projectStatus['on_hold'] > 0): ?>
                    <span class="hz-badge hz-badge--orange"><?= $projectStatus['on_hold'] ?> on hold</span>
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if ($customerGrowth !== null): ?>
        <div class="hz-card hz-card--stat">
            <p class="hz-card__label">Klantengroei (<?= htmlspecialchars($customerGrowth['label']) ?>)</p>
            <p class="hz-card__value"><?= $customerGrowth['total'] ?> totaal</p>
            <p style="font-size:.8rem;margin-top:.25rem;">
                <span class="hz-badge <?= $customerGrowth['growth_pct'] >= 0 ? 'hz-badge--green' : 'hz-badge--red' ?>">
                    <?= $customerGrowth['growth_pct'] >= 0 ? '+' : '' ?><?= $customerGrowth['growth_pct'] ?>% t.o.v. vorige maand
                </span>
            </p>
        </div>
    <?php endif; ?>

    <?php if ($revenue === null && $outstanding === null && $projectStatus === null && $customerGrowth === null): ?>
        <div class="hz-card" style="grid-column:1/-1;">
            <p style="color:var(--hz-text-muted);margin:0;">
                Nog geen KPI-gegevens beschikbaar. Activeer modules zoals Facturatie of Projecten via
                <a href="<?= BASE ?>/beheer/modules" style="color:var(--hz-primary);">Beheer &rsaquo; Modules</a> om hier cijfers te zien.
            </p>
        </div>
    <?php endif; ?>
</div>

<h2 style="font-size:1.05rem;margin-bottom:.75rem;">Jouw modules</h2>
<div class="hz-grid hz-grid--3">
    <?php foreach ($modules as $key => $module): ?>
        <a href="<?= BASE . '/' . $key ?>" class="hz-card" style="text-decoration:none;color:inherit;display:flex;gap:1rem;align-items:flex-start;">
            <div style="width:44px;height:44px;border-radius:.6rem;background:var(--hz-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--hz-primary);">
                <?= hz_icon($module['icon'] ?? 'box') ?>
            </div>
            <div>
                <h3 style="margin:0 0 .25rem;font-size:.95rem;"><?= htmlspecialchars($module['name']) ?></h3>
                <p style="margin:0;color:var(--hz-text-muted);font-size:.82rem;"><?= htmlspecialchars($module['description']) ?></p>
            </div>
        </a>
    <?php endforeach; ?>

    <a href="<?= BASE ?>/beheer/modules" class="hz-card" style="text-decoration:none;color:inherit;display:flex;gap:1rem;align-items:center;border-style:dashed;">
        <div style="width:44px;height:44px;border-radius:.6rem;background:var(--hz-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--hz-text-muted);">
            <?= hz_icon('plus') ?>
        </div>
        <div>
            <h3 style="margin:0;font-size:.95rem;">Meer modules activeren</h3>
            <p style="margin:0;color:var(--hz-text-muted);font-size:.82rem;">Bekijk de volledige module-store</p>
        </div>
    </a>
</div>
<?php View::partial('footer'); ?>
