<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script>
    (function() {
        const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    })();
</script>

<header class="topbar">
    <div class="topbar__left">
        <a href="<?= isset($logoHref) ? $logoHref : '?c=tableauDeBord' ?>" class="topbar__logo">
            <span class="appicon" aria-hidden="true"></span>
            <span class="apptitle">QUESTIONARY</span>
        </a>
    </div>

    <div class="topbar__right" aria-label="Université de Limoges">
        <button id="theme-toggle" class="theme-toggle" title="Basculer en mode sombre/clair" aria-label="Changer de thème">
            <i class="fa-solid fa-moon icon-moon"></i>
            <i class="fa-solid fa-sun icon-sun"></i>
        </button>
        <span class="uni-badge" aria-hidden="true">uℓ</span>
        <span class="uni-text">Université de Limoges</span>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('theme-toggle');
        
        toggle.addEventListener('click', () => {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const newTheme = isDark ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    });
</script>
