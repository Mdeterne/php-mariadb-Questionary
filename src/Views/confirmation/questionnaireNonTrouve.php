<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionnaire Introuvable - Questionary</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/pages/not_found.css">
</head>

<body>


    <header class="topbar">
        <div class="topbar__left">
            <a href="index.php" class="topbar__logo">
                <span class="appicon" aria-hidden="true"></span>
                <span class="apptitle">QUESTIONARY</span>
            </a>

        </div>

        <div class="topbar__right" aria-label="Université de Limoges">
            <span class="uni-badge" aria-hidden="true">uℓ</span>
            <span class="uni-text">Université de Limoges</span>
        </div>
    </header>
    <div class="app-container" style="justify-content: center; align-items: center;">
        <main class="main-content" style="max-width: 600px; width: 100%;">

            <div class="card error-container">
                <i class="fa-solid fa-file-circle-question error-icon"></i>
                <h1 class="error-title">Oups !</h1>
                <p class="error-desc">Le questionnaire que vous recherchez semble introuvable. Le code PIN est peut-être
                    incorrect ou le questionnaire a été fermé.</p>

                <a href="index.php" class="btn-home">
                    <i class="fa-solid fa-house"></i> Retour à l'accueil
                </a>
            </div>

        </main>
    </div>
</body>

</html>