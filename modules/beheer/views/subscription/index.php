<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Abonnement', 'activeModule' => 'beheer']);
require __DIR__ . '/../_nav.php';
beheerNav('subscription');
?>
<h1 style="font-size:1.2rem;margin:0 0 .25rem;">Abonnement</h1>
<p style="color:var(--hz-text-muted);margin:0 0 1.25rem;">
    Demo-simulatie — geen echte betaalintegratie (Mollie/Stripe). Een planwissel wordt direct doorgevoerd.
</p>

<?php if ($flash): ?>
    <div class="hz-card" style="border-left:4px solid var(--hz-success);margin-bottom:1.25rem;"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="hz-grid hz-grid--3">
    <?php foreach ($plans as $slug => $plan): ?>
        <div class="hz-card" style="display:flex;flex-direction:column;gap:.75rem;<?= $slug === $currentSlug ? 'border-color:var(--hz-primary);' : '' ?>">
            <div>
                <h3 style="margin:0 0 .2rem;font-size:1.05rem;"><?= htmlspecialchars($plan['name']) ?></h3>
                <p style="margin:0;color:var(--hz-text-muted);font-size:.85rem;"><?= htmlspecialchars($plan['price_label']) ?></p>
            </div>
            <p style="font-size:.82rem;color:var(--hz-text-muted);margin:0;">
                <?= $plan['max_users'] === null ? 'Onbeperkt aantal gebruikers' : 'Tot ' . $plan['max_users'] . ' gebruikers' ?><br>
                <?= $plan['modules'] === '*' ? 'Alle modules, incl. uitbreidingen' : count($plan['modules']) . ' modules' ?>
            </p>
            <div style="margin-top:auto;">
                <?php if ($slug === $currentSlug): ?>
                    <span class="hz-badge hz-badge--green">Huidig abonnement</span>
                    <?php if ($slug !== 'starter' && $currentUsers <= 2): ?>
                        <p style="font-size:.75rem;color:var(--hz-text-muted);margin-top:.4rem;"><?= $currentUsers ?> gebruiker(s) actief</p>
                    <?php endif; ?>
                <?php else: ?>
                    <form method="post" action="<?= BASE ?>/beheer/abonnement"><?= \Core\Csrf::field() ?>
                        <input type="hidden" name="plan" value="<?= htmlspecialchars($slug) ?>">
                        <button type="submit" class="hz-btn hz-btn--primary" data-hz-confirm="Overstappen naar <?= htmlspecialchars($plan['name']) ?>? (demo-simulatie, geen echte betaling)">
                            Kies <?= htmlspecialchars($plan['name']) ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php View::partial('footer'); ?>
