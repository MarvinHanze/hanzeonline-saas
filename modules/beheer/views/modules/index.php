<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Modules', 'activeModule' => 'beheer']);
require __DIR__ . '/../_nav.php';
beheerNav('modules');
?>
<h1 style="font-size:1.2rem;margin:0 0 .25rem;">Module-store</h1>
<p style="color:var(--hz-text-muted);margin:0 0 1.25rem;">
    Je huidige abonnement: <strong><?= htmlspecialchars($planLabel) ?></strong>.
    <a href="<?= BASE ?>/beheer/abonnement" style="color:var(--hz-primary);">Abonnement wijzigen</a>
</p>

<?php if ($flash): ?>
    <div class="hz-card" style="border-left:4px solid var(--hz-warning);margin-bottom:1.25rem;">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div class="hz-grid hz-grid--3">
    <?php foreach ($modules as $key => $m): ?>
        <div class="hz-card" style="display:flex;flex-direction:column;gap:.75rem;">
            <div style="display:flex;gap:.75rem;align-items:flex-start;">
                <div style="width:40px;height:40px;border-radius:.6rem;background:var(--hz-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--hz-primary);">
                    <?= hz_icon($m['icon'] ?? 'box') ?>
                </div>
                <div>
                    <h3 style="margin:0 0 .2rem;font-size:.95rem;"><?= htmlspecialchars($m['name']) ?></h3>
                    <p style="margin:0;color:var(--hz-text-muted);font-size:.8rem;"><?= htmlspecialchars($m['description']) ?></p>
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;">
                <?php if (!empty($m['is_core'])): ?>
                    <span class="hz-badge hz-badge--gray">Kernmodule</span>
                <?php elseif (!empty($m['placeholder'])): ?>
                    <span class="hz-badge hz-badge--gray">Binnenkort</span>
                <?php elseif ($m['is_enabled']): ?>
                    <span class="hz-badge hz-badge--green">Actief</span>
                <?php elseif (!$m['allowed_by_plan']): ?>
                    <span class="hz-badge hz-badge--orange">Niet in je abonnement</span>
                <?php else: ?>
                    <span class="hz-badge hz-badge--gray">Uit</span>
                <?php endif; ?>

                <?php if (empty($m['is_core']) && empty($m['placeholder'])): ?>
                    <?php if ($m['is_enabled']): ?>
                        <form method="post" action="<?= BASE ?>/beheer/modules/<?= htmlspecialchars($key) ?>/deactiveren"><?= \Core\Csrf::field() ?>
                            <button type="submit" class="hz-btn hz-btn--ghost" data-hz-confirm="Module '<?= htmlspecialchars($m['name']) ?>' uitschakelen voor dit account?">Uitschakelen</button>
                        </form>
                    <?php elseif ($m['allowed_by_plan']): ?>
                        <form method="post" action="<?= BASE ?>/beheer/modules/<?= htmlspecialchars($key) ?>/activeren"><?= \Core\Csrf::field() ?>
                            <button type="submit" class="hz-btn hz-btn--primary">Activeren</button>
                        </form>
                    <?php else: ?>
                        <a href="<?= BASE ?>/beheer/abonnement" class="hz-btn hz-btn--outline">Upgraden</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php View::partial('footer'); ?>
