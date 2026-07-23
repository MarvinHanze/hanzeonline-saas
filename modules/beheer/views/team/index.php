<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Team', 'activeModule' => 'beheer']);
require __DIR__ . '/../_nav.php';
beheerNav('team');
?>
<h1 style="font-size:1.2rem;margin:0 0 .25rem;">Teamleden</h1>
<p style="color:var(--hz-text-muted);margin:0 0 1.25rem;">
    <?= count($members) ?><?= $maxUsers !== null ? ' / ' . $maxUsers : '' ?> gebruikers.
    <?php if ($maxUsers !== null && count($members) >= $maxUsers): ?>
        <a href="<?= BASE ?>/beheer/abonnement" style="color:var(--hz-primary);">Upgrade voor meer gebruikers</a>
    <?php endif; ?>
</p>

<?php if ($flash): ?>
    <div class="hz-card" style="border-left:4px solid var(--hz-warning);margin-bottom:1.25rem;"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="hz-card" style="margin-bottom:1.5rem;">
    <table class="hz-table">
        <thead>
            <tr><th>Naam</th><th>E-mail</th><th>Rol</th><th>Laatst ingelogd</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($members as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['name']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td>
                        <form method="post" action="<?= BASE ?>/beheer/team/<?= (int) $m['id'] ?>/rol" style="display:flex;gap:.4rem;align-items:center;"><?= \Core\Csrf::field() ?>
                            <select name="role" onchange="this.form.submit()" <?= (int) $m['id'] === $currentUserId ? 'disabled' : '' ?>>
                                <?php foreach (['owner', 'admin', 'user'] as $r): ?>
                                    <option value="<?= $r ?>" <?= $m['role'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                    <td><?= $m['last_login'] ? htmlspecialchars($m['last_login']) : '—' ?></td>
                    <td>
                        <?php if ((int) $m['id'] !== $currentUserId): ?>
                            <form method="post" action="<?= BASE ?>/beheer/team/<?= (int) $m['id'] ?>/verwijderen"><?= \Core\Csrf::field() ?>
                                <button type="submit" class="hz-btn hz-btn--ghost" data-hz-confirm="<?= htmlspecialchars($m['name']) ?> verwijderen uit dit team?">Verwijderen</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="hz-card" style="max-width:420px;">
    <h2 style="font-size:1rem;margin:0 0 .75rem;">Teamlid uitnodigen</h2>
    <form method="post" action="<?= BASE ?>/beheer/team"><?= \Core\Csrf::field() ?>
        <div class="hz-field"><input type="text" name="name" placeholder=" " required><label>Naam</label></div>
        <div class="hz-field"><input type="email" name="email" placeholder=" " required><label>E-mailadres</label></div>
        <div class="hz-field"><input type="password" name="password" placeholder=" " required minlength="8"><label>Tijdelijk wachtwoord</label></div>
        <div style="margin-bottom:1rem;">
            <label style="font-size:.85rem;font-weight:600;display:block;margin-bottom:.35rem;">Rol</label>
            <select name="role">
                <option value="user">Gebruiker</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="hz-btn hz-btn--primary">Toevoegen</button>
    </form>
</div>
<?php View::partial('footer'); ?>
