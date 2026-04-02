<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionnaire Introuvable - Questionary</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/components/topbar.css">
    <link rel="stylesheet" href="css/components/buttons.css">
    <link rel="stylesheet" href="css/pages/not_found.css">
</head>

<body>
    <?php
    $logoHref = 'index.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Composants' . DIRECTORY_SEPARATOR . 'header.php';
    ?>

    <div class="app-container" style="display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 64px); padding: 20px;">
        <main style="max-width: 600px; width: 100%;">
            <div class="error-container">
                <i class="fa-solid fa-file-circle-question error-icon"></i>
                <h1 class="error-title">Oups !</h1>
                <p class="error-desc">Le questionnaire que vous recherchez semble introuvable. Le code PIN est peut-être incorrect ou le questionnaire a été fermé.</p>

                <a href="index.php" class="btn-home">
                    <i class="fa-solid fa-house"></i> Retour à l'accueil
                </a>
            </div>
        </main>
    </div>
</body>

</html>