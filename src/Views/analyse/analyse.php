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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.2.2/wordcloud2.min.js"></script>
</head>

<body>

    <?php require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Composants' . DIRECTORY_SEPARATOR . 'header.php'; ?>

    <main>
        <div class="top-controls">
            <div class="header-content">
                <span class="page-label">Analyse des résultats</span>
                <h1 class="page-title"><?php echo htmlspecialchars($pageTitle ?? 'Questionnaire sans titre', ENT_QUOTES, 'UTF-8'); ?></h1>
            </div>
            <div class="buttons-group" style="display: flex; gap: 12px; align-items: center;">
                <button type="button" id="btn-export" style="background-color: #2e7d32; color: #fff; border: none; display: flex; gap: 8px; align-items: center; border-radius: 8px; padding: 0 20px; font-weight: 600; cursor: pointer; height: 44px; margin: 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: opacity 0.2s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Exporter les résultats
                </button>
                <a href="?c=tableauDeBord" class="btn-primary" style="margin: 0; display: flex; gap: 8px; align-items: center; border-radius: 8px; padding: 0 20px; font-weight: 600; text-decoration: none; height: 44px; text-align: center; border: none; line-height: 44px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Retour au tableau de bord
                </a>
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

    <!-- Modale d'exportation -->
    <div id="export-modal" class="modal-overlay">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
            <div class="modal-header">
                <h3>Exporter les réponses</h3>
                <button type="button" id="btn-close-export" class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body" style="padding: 20px; display: flex; flex-direction: column; gap: 15px;">
                <p>Choisissez le format d'exportation :</p>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button onclick="window.location.href='export_csv.php?id=<?php echo $idQuestionnaire; ?>';" style="background-color: #2e7d32; color: #fff; border: none; display: flex; align-items: center; justify-content: center; flex: 1; border-radius: 8px; padding: 12px; font-weight: 600; cursor: pointer; transition: opacity 0.2s;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        CSV
                    </button>
                    <button onclick="window.location.href='export_xlsx.php?id=<?php echo $idQuestionnaire; ?>';" style="background-color: #107c41; color: #fff; border: none; display: flex; align-items: center; justify-content: center; flex: 1; border-radius: 8px; padding: 12px; font-weight: 600; cursor: pointer; transition: opacity 0.2s;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <circle cx="10" cy="13" r="2"></circle>
                            <circle cx="14" cy="17" r="2"></circle>
                            <path d="M14 13l-4 4"></path>
                        </svg>
                        Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>