<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionary - Analyse</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <header class="topbar">
        <div class="topbar__left">
            <a href="?c=tableauDeBord" class="topbar__logo">
                <span class="appicon" aria-hidden="true"></span>
                <span class="apptitle">QUESTIONARY</span>
            </a>
        </div>

        <div class="topbar__right" aria-label="Université de Limoges">
            <span class="uni-badge" aria-hidden="true">uℓ</span>
            <span class="uni-text">Université de Limoges</span>
        </div>
    </header>

    <main>
        <div class="top-controls">
            <div>
                <h2>Analyse du questionnaire : QUESTIONNAIRE 1</h2>
            </div>
            <div class="buttons-group">
                <button class="btn">Générer le questionnaire avec les réponses les plus revenues</button>
                <button class="btn">Quitter</button>
            </div>
        </div>

        <div class="stats-box">
            Nombre total de réponse : ??????
        </div>

        <div class="cards-container" id="cards-wrapper">
            </div>
    </main>

    <script src="js/analyse.js"></script>
</body>
</html>