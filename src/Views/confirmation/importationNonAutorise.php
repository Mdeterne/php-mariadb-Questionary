<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import impossible - Questionary</title>

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/components/topbar.css">
    <link rel="stylesheet" href="css/components/buttons.css">
    <link rel="stylesheet" href="css/components/modals.css">
    <style>
        body {
            background-color: var(--background, #f8f9fa);
            font-family: 'Inter', sans-serif;
            margin: 0;
            height: 100vh;
            overflow: hidden;
            /* Pour ne pas scroll quand la popup est la */
        }
    </style>
</head>

<body>

    <?php require_once __DIR__ . '/../components/header.php'; ?>

    <div class="modal-blur-overlay">
        <div class="modal-card">
            <div>
                <h3 class="modal-title">Importation impossible</h3>
                <p class="modal-desc" style="margin-top: 10px;">
                    Le questionnaire n'a pas pu être importé. Vérifiez le code PIN ou assurez-vous que vous n'êtes pas déjà le propriétaire.
                </p>
            </div>

            <div class="modal-actions">
                <a href="?c=tableauDeBord" class="btn-confirm"
                    style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                    Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>

</body>

</html>