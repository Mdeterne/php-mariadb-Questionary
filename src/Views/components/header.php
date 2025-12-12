<header class="topbar">
    <div class="topbar__left">
        <a href="<?= isset($logoHref) ? $logoHref : '?c=tableauDeBord' ?>" class="topbar__logo">
            <span class="appicon" aria-hidden="true"></span>
            <span class="apptitle">QUESTIONARY</span>
        </a>
        <?php if (isset($showCreateLink) && $showCreateLink): ?>
            <a class="topbar__create-link" href="?c=tableauDeBord">Créer un questionnaire</a>
        <?php endif; ?>
    </div>

    <div class="topbar__right" aria-label="Université de Limoges">
        <span class="uni-badge" aria-hidden="true">uℓ</span>
        <span class="uni-text">Université de Limoges</span>
    </div>
</header>
