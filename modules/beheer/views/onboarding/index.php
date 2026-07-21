<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Welkom', 'activeModule' => 'beheer']);
?>
<div style="max-width:560px;margin:0 auto;">
    <div style="display:flex;gap:.4rem;margin-bottom:1.5rem;">
        <?php for ($i = 0; $i < $totalSteps; $i++): ?>
            <div style="flex:1;height:6px;border-radius:999px;background:<?= $i <= $step ? 'var(--hz-primary)' : 'var(--hz-border)' ?>;"></div>
        <?php endfor; ?>
    </div>

    <?php if ($step === 0): ?>
        <h1 style="font-size:1.3rem;margin:0 0 .35rem;">Welkom bij HanzeOS, <?= htmlspecialchars($tenant['name'] ?? '') ?>!</h1>
        <p style="color:var(--hz-text-muted);margin:0 0 1.5rem;">Stap 1 van <?= $totalSteps ?>: kies een merkkleur voor je omgeving.</p>
        <div class="hz-card">
            <form method="post" action="<?= BASE ?>/beheer/onboarding">
                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-size:.85rem;font-weight:600;margin-bottom:.35rem;">Merkkleur</label>
                    <input type="color" name="brand_color" value="<?= htmlspecialchars($tenant['brand_color'] ?? '#2563eb') ?>" style="width:70px;height:38px;padding:0;border:1px solid var(--hz-border);border-radius:var(--hz-radius);cursor:pointer;">
                </div>
                <button type="submit" class="hz-btn hz-btn--primary">Volgende</button>
            </form>
        </div>

    <?php elseif ($step === 1): ?>
        <h1 style="font-size:1.3rem;margin:0 0 .35rem;">Welke modules wil je gebruiken?</h1>
        <p style="color:var(--hz-text-muted);margin:0 0 1.5rem;">Stap 2 van <?= $totalSteps ?>: activeer direct modules die bij je abonnement horen. Je kunt dit later altijd wijzigen via Beheer &gt; Modules.</p>
        <div class="hz-card">
            <form method="post" action="<?= BASE ?>/beheer/onboarding">
                <div style="display:flex;flex-direction:column;gap:.6rem;margin-bottom:1.25rem;">
                    <?php foreach ($modules as $key => $m): ?>
                        <?php if (!empty($m['is_core']) || !empty($m['placeholder']) || !$m['allowed_by_plan']) continue; ?>
                        <label style="display:flex;align-items:center;gap:.6rem;">
                            <input type="checkbox" class="hz-checkbox" name="modules[]" value="<?= htmlspecialchars($key) ?>" <?= $m['is_enabled'] ? 'checked' : '' ?>>
                            <span><?= htmlspecialchars($m['name']) ?> — <span style="color:var(--hz-text-muted);font-size:.82rem;"><?= htmlspecialchars($m['description']) ?></span></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="hz-btn hz-btn--primary">Volgende</button>
            </form>
        </div>

    <?php else: ?>
        <h1 style="font-size:1.3rem;margin:0 0 .35rem;">Helemaal klaar!</h1>
        <p style="color:var(--hz-text-muted);margin:0 0 1.5rem;">Stap <?= $totalSteps ?> van <?= $totalSteps ?>: je omgeving staat klaar.</p>
        <div class="hz-card">
            <form method="post" action="<?= BASE ?>/beheer/onboarding">
                <button type="submit" class="hz-btn hz-btn--primary">Naar het dashboard</button>
            </form>
        </div>
    <?php endif; ?>
</div>
<?php View::partial('footer'); ?>
