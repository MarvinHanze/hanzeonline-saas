<?php
declare(strict_types=1);

/**
 * Sluit de app-shell die _partials/header.php opent (sidebar + topbar +
 * .hz-shell__content) en laadt de gedeelde componenten-JS. Gebruik samen met
 * header.php in elke nieuwe module-view:
 *
 *   View::partial('header', ['pageTitle' => '...', 'activeModule' => 'crm']);
 *   ... pagina-inhoud ...
 *   View::partial('footer');
 */
?>
        </div>
    </div>
</div>
<script src="<?= BASE ?>/assets/js/components.js"></script>
</body>
</html>
