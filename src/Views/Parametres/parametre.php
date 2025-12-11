<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres du Questionnaire</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/components/topbar.css">
    <link rel="stylesheet" href="css/components/buttons.css">
    <link rel="stylesheet" href="css/components/inputs.css">
    <link rel="stylesheet" href="css/components/cards.css">
    <link rel="stylesheet" href="css/pages/settings.css">
</head>

<body>

    <?php require_once __DIR__ . '/../components/header.php'; ?>

    <main class="settings-main">
        <div class="page-title-row">
            <span class="page-title-label">Paramètres du Questionnaire :</span>
            <span class="page-title-value">QUESTIONNAIRE 1</span>
        </div>

        <div class="section">
            <h3>Partage</h3>
            <div class="card-box">
                <label class="sub-label">Lien de partage</label>
                <div class="input-group">
                    <input type="text" id="share-link" value="https://questionary.unilim.fr/q/12345" readonly>
                    <button class="btn-copy" id="btn-copy">Copier</button>
                </div>
            </div>
        </div>

        <div class="section">
            <h3>Accès</h3>
            <div class="card-box toggle-row">
                <label for="toggle-access">Accepte les réponses</label>
                <label class="switch">
                    <input type="checkbox" id="toggle-access" checked>
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <div class="section">
            <h3>Configuration</h3>
            <div class="card-box">
                <div class="date-row">
                    <div class="date-col">
                        <label class="sub-label">Date de début</label>
                        <input type="date" id="date-start">
                    </div>
                    <div class="date-col">
                        <label class="sub-label">Date de fin</label>
                        <input type="date" id="date-end">
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <h3>Notifications</h3>

            <div class="card-box toggle-row">
                <label for="notif-response">Notification de réponse</label>
                <label class="switch">
                    <input type="checkbox" id="notif-response" checked>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="card-box toggle-row">
                <label for="notif-limit">Seuil atteint</label>
                <label class="switch">
                    <input type="checkbox" id="notif-limit" checked>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="card-box toggle-row">
                <label for="notif-invalid">Réponses invalides</label>
                <label class="switch">
                    <input type="checkbox" id="notif-invalid" checked>
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn btn-cancel" id="btn-cancel" onclick="window.history.back()">Annuler les
                modifications</button>
            <a href="index.php?c=createur&a=index&id=99">
                <button class="btn btn-save" id="btn-save">Enregistrer les modifications</button>
            </a>
        </div>

        <div class="delete-zone">
            <button class="btn-delete-link" id="btn-delete">Supprimer le questionnaire</button>
        </div>

    </main>

    <script src="js/parametre.js"></script>
</body>

</html>