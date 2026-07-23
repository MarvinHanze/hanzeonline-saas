<?php
declare(strict_types=1);

use Core\View;

View::partial('header', ['pageTitle' => 'Branding', 'activeModule' => 'beheer']);
require __DIR__ . '/../_nav.php';
beheerNav('branding');
?>
<h1 style="font-size:1.2rem;margin:0 0 1rem;">Branding / white-labeling</h1>

<div class="hz-card" style="max-width:520px;">
    <form method="post" action="<?= BASE ?>/beheer/branding" enctype="multipart/form-data"><?= \Core\Csrf::field() ?>
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
            <?php if (!empty($tenant['logo_path'])): ?>
                <img src="<?= BASE . '/' . htmlspecialchars($tenant['logo_path']) ?>" alt="Logo" style="width:56px;height:56px;border-radius:.6rem;object-fit:cover;border:1px solid var(--hz-border);">
            <?php else: ?>
                <div style="width:56px;height:56px;border-radius:.6rem;background:<?= htmlspecialchars($tenant['brand_color'] ?? '#2563eb') ?>;"></div>
            <?php endif; ?>
            <div>
                <label for="logo" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:.35rem;">Logo (PNG/JPG/WebP, max 2MB)</label>
                <input type="file" name="logo" id="logo" accept="image/png,image/jpeg,image/webp">
            </div>
        </div>

        <div style="margin-bottom:1.25rem;">
            <label for="brand_color" style="display:block;font-size:.85rem;font-weight:600;margin-bottom:.35rem;">Merkkleur</label>
            <input type="color" name="brand_color" id="brand_color" value="<?= htmlspecialchars($tenant['brand_color'] ?? '#2563eb') ?>" style="width:70px;height:38px;padding:0;border:1px solid var(--hz-border);border-radius:var(--hz-radius);cursor:pointer;">
            <span style="color:var(--hz-text-muted);font-size:.8rem;margin-left:.5rem;">Wordt gebruikt in de sidebar en accentkleuren.</span>
        </div>

        <button type="submit" class="hz-btn hz-btn--primary">Opslaan</button>
    </form>
</div>
<?php View::partial('footer'); ?>
