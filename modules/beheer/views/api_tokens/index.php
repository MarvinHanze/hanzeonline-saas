<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'API-tokens', 'activeModule' => 'beheer']);
require __DIR__ . '/../_nav.php';
beheerNav('api_tokens');
?>
<h1 style="font-size:1.2rem;margin:0 0 .25rem;">API-tokens</h1>
<p style="color:var(--hz-text-muted);margin:0 0 1.25rem;">
    Voor partnerintegraties via de REST-API (<code>/api/v1/...</code>). Stuur mee als <code>Authorization: Bearer &lt;token&gt;</code>.
</p>

<?php if ($newToken): ?>
    <div class="hz-card" style="border-left:4px solid var(--hz-success);margin-bottom:1.25rem;">
        <strong>Nieuw token aangemaakt.</strong> Bewaar het nu — het wordt hierna niet meer volledig getoond:
        <div style="font-family:monospace;background:var(--hz-bg);padding:.6rem .8rem;border-radius:var(--hz-radius);margin-top:.5rem;word-break:break-all;">
            <?= htmlspecialchars($newToken) ?>
        </div>
    </div>
<?php endif; ?>

<div class="hz-card" style="margin-bottom:1.5rem;">
    <table class="hz-table">
        <thead><tr><th>Naam</th><th>Laatst gebruikt</th><th>Aangemaakt</th><th>Status</th><th></th></tr></thead>
        <tbody>
            <?php if (empty($tokens)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--hz-text-muted);padding:1.5rem;">Nog geen API-tokens aangemaakt</td></tr>
            <?php else: foreach ($tokens as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['name']) ?></td>
                    <td><?= $t['last_used_at'] ? htmlspecialchars($t['last_used_at']) : 'Nooit' ?></td>
                    <td><?= htmlspecialchars($t['created_at']) ?></td>
                    <td>
                        <?php if ($t['revoked_at']): ?>
                            <span class="hz-badge hz-badge--red">Ingetrokken</span>
                        <?php else: ?>
                            <span class="hz-badge hz-badge--green">Actief</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$t['revoked_at']): ?>
                            <form method="post" action="<?= BASE ?>/beheer/api-tokens/<?= (int) $t['id'] ?>/intrekken"><?= \Core\Csrf::field() ?>
                                <button type="submit" class="hz-btn hz-btn--ghost" data-hz-confirm="Token '<?= htmlspecialchars($t['name']) ?>' intrekken? API-toegang met dit token stopt direct.">Intrekken</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div class="hz-card" style="max-width:420px;">
    <h2 style="font-size:1rem;margin:0 0 .75rem;">Nieuw token aanmaken</h2>
    <form method="post" action="<?= BASE ?>/beheer/api-tokens"><?= \Core\Csrf::field() ?>
        <div class="hz-field"><input type="text" name="name" placeholder=" " required><label>Naam (bv. "Boekhoudkoppeling")</label></div>
        <button type="submit" class="hz-btn hz-btn--primary">Token genereren</button>
    </form>
</div>
<?php View::partial('footer'); ?>
