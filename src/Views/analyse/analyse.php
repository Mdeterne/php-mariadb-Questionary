<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionary - Analyse</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/components/topbar.css">
    <link rel="stylesheet" href="css/components/buttons.css">
    <link rel="stylesheet" href="css/components/inputs.css">
    <link rel="stylesheet" href="css/components/cards.css">
    <link rel="stylesheet" href="css/pages/analysis.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <?php require_once __DIR__ . '/../components/header.php'; ?>

    <main>
        <div class="top-controls">
            <div class="header-content">
                <span class="page-label">Analyse des résultats</span>
                <h1 class="page-title"><?php echo htmlspecialchars($pageTitle ?? 'Questionnaire sans titre'); ?></h1>
            </div>
            <div class="buttons-group">
                <a href="?c=tableauDeBord" class="btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Retour au tableau de bord
                </a>
            </div>
        </div>

        <div class="stats-box">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <span><strong><?php echo $responseCount ?? 0; ?></strong> réponses collectées</span>
        </div>

        <div class="cards-container" id="cards-wrapper">
            <?php
            $data = isset($questionsData) ? json_decode($questionsData) : [];
            if (empty($data)):
                ?>
                <p style="text-align:center; color:#666; margin-top:50px;">Aucune question trouvée ou pas encore de
                    réponses.</p>
            <?php endif; ?>
        </div>
    </main>

    <script>
        window.surveyData = <?php echo $questionsData ?? '[]'; ?>;
    </script>
    <script src="js/analyse.js?v=<?php echo time(); ?>"></script>
</body>

</html>