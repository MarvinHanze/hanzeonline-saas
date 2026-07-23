<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Beveiliging', 'activeModule' => 'beheer']);
require __DIR__ . '/../_nav.php';
beheerNav('security');
?>
<h1 style="font-size:1.2rem;margin:0 0 .25rem;">Tweestapsverificatie (2FA)</h1>
<p style="color:var(--hz-text-muted);margin:0 0 1.25rem;">Alleen voor je eigen account.</p>

<?php if ($flash): ?>
    <div class="hz-card" style="border-left:4px solid var(--hz-primary);margin-bottom:1.25rem;"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="hz-card" style="max-width:480px;">
    <?php if ($totpEnabled): ?>
        <p><span class="hz-badge hz-badge--green">Ingeschakeld</span></p>
        <p style="color:var(--hz-text-muted);font-size:.88rem;">
            Je account is beveiligd met een authenticator-app. Schakel 2FA alleen uit als je toegang tot je authenticator kwijt bent.
        </p>
        <form method="post" action="<?= BASE ?>/beheer/beveiliging/uitschakelen"><?= \Core\Csrf::field() ?>
            <button type="submit" class="hz-btn hz-btn--danger" data-hz-confirm="2FA uitschakelen? Je account is dan minder goed beveiligd.">Uitschakelen</button>
        </form>
    <?php else: ?>
        <p style="color:var(--hz-text-muted);font-size:.88rem;margin-bottom:1rem;">
            Scan onderstaande sleutel met een authenticator-app (Google Authenticator, Authy, 1Password, ...) via
            "handmatig toevoegen", en vul de 6-cijferige code in om te bevestigen.
        </p>
        <div style="font-family:monospace;background:var(--hz-bg);padding:.6rem .8rem;border-radius:var(--hz-radius);margin-bottom:1rem;word-break:break-all;">
            <?= htmlspecialchars($formattedSecret) ?>
        </div>
        <p style="font-size:.78rem;color:var(--hz-text-muted);word-break:break-all;margin-bottom:1.25rem;">
            <?= htmlspecialchars($provisioningUri) ?>
        </p>
        <form method="post" action="<?= BASE ?>/beheer/beveiliging/inschakelen" style="display:flex;gap:.5rem;align-items:flex-end;"><?= \Core\Csrf::field() ?>
            <div class="hz-field" style="margin-bottom:0;flex:1;">
                <input type="text" name="code" placeholder=" " required inputmode="numeric" maxlength="6" pattern="[0-9]*">
                <label>Verificatiecode</label>
            </div>
            <button type="submit" class="hz-btn hz-btn--primary">Bevestigen</button>
        </form>
    <?php endif; ?>
</div>
<?php View::partial('footer'); ?>
