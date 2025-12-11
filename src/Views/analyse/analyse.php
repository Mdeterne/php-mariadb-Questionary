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
    <link rel="stylesheet" href="css/pages/analysis.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <?php require_once __DIR__ . '/../components/header.php'; ?>

    <main>
        <div class="top-controls">
            <div>
                <h2>Analyse du questionnaire : <?php echo htmlspecialchars($pageTitle ?? 'Titre inconnu'); ?></h2>
            </div>
            <div class="buttons-group">
                <!-- <button class="btn">Générer le questionnaire avec les réponses les plus revenues</button> -->
                <a href="?c=tableauDeBord" class="btn">Quitter</a>
            </div>
        </div>

        <div class="stats-box">
            Nombre total de réponses : <strong><?php echo $responseCount ?? 0; ?></strong>
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
    <script src="js/analyse.js"></script>
</body>

</html>