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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.2.2/wordcloud2.min.js"></script>
</head>

<body>

    <?php require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Composants' . DIRECTORY_SEPARATOR . 'header.php'; ?>

    <main>
        <div class="top-controls">
            <div class="header-content">
                <span class="page-label">Analyse des résultats</span>
                <h1 class="page-title"><?php echo htmlspecialchars($pageTitle ?? 'Questionnaire sans titre'); ?></h1>
            </div>
            <div class="buttons-group" style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                <a href="?c=tableauDeBord" class="btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Retour au tableau de bord
                </a>
                <button onclick="window.location.href='export_pdf.php?id=<?php echo $idQuestionnaire; ?>'" class="btn-secondary" style="background-color: #e53e3e; border-color: #e53e3e;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Exporter en PDF
                </button>
            </div>
        </div>

        <div class="stats-box" id="stats-area">
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
    <script src="js/load.php?f=analyse.js&v=<?php echo time(); ?>"></script>

    <!-- Modale d'affichage des réponses textuelles -->
    <div id="text-answers-modal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title-text">Réponses</h3>
                <button class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <ul id="modal-text-list" class="text-answers-list-modal"></ul>
            </div>
        </div>
    </div>
</body>

</html>