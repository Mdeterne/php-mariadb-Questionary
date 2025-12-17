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
    <link rel="stylesheet" href="css/components/modals.css">
    <link rel="stylesheet" href="css/pages/settings.css">
</head>

<body>

    <?php require_once __DIR__ . '/../components/header.php'; ?>

    <?php
    $settings = isset($survey['settings']) ? json_decode($survey['settings'], true) : [];
    $isActive = isset($survey['status']) && $survey['status'] === 'active';
    ?>

    <main class="settings-main" data-survey-id="<?php echo $survey['id']; ?>">
        <div class="page-title-row">
            <span class="page-title-label">Paramètres du Questionnaire :</span>
            <span class="page-title-value"><?php echo htmlspecialchars($survey['title']); ?></span>
        </div>

        <div class="section">
            <h3>Partage</h3>
            <div class="card-box">
                <label class="sub-label">Lien de partage</label>
                <div class="input-group">
                    <input type="text" id="share-link" value="<?php echo "http://" . $_SERVER['HTTP_HOST'] . "/?c=home&q=" . ($survey['access_pin'] ?? 'TEST01'); ?>" readonly>
                    <button class="btn-copy" id="btn-copy">Copier</button>
                </div>
            </div>
        </div>

        <div class="section">
            <h3>Accès</h3>
            <div class="card-box toggle-row">
                <label for="toggle-access">Accepte les réponses</label>
                <label class="switch">
                    <input type="checkbox" id="toggle-access" <?php echo $isActive ? 'checked' : ''; ?>>
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
                        <input type="date" id="date-start" value="<?php echo $settings['dateStart'] ?? ''; ?>">
                    </div>
                    <div class="date-col">
                        <label class="sub-label">Date de fin</label>
                        <input type="date" id="date-end" value="<?php echo $settings['dateEnd'] ?? ''; ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <h3>Notifications</h3>

            <div class="card-box toggle-row">
                <label for="notif-response">Notification de réponse</label>
                <label class="switch">
                    <input type="checkbox" id="notif-response" <?php echo ($settings['notifResponse'] ?? false) ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="card-box toggle-row">
                <label for="notif-limit">Seuil atteint</label>
                <label class="switch">
                    <input type="checkbox" id="notif-limit" <?php echo ($settings['notifLimit'] ?? false) ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="card-box toggle-row">
                <label for="notif-invalid">Réponses invalides</label>
                <label class="switch">
                    <input type="checkbox" id="notif-invalid" <?php echo ($settings['notifInvalid'] ?? false) ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn btn-cancel" id="btn-cancel">Annuler les
                modifications</button>
            <button class="btn btn-save" id="btn-save">Enregistrer les modifications</button>
        </div>

        <div class="delete-zone">
            <button class="btn-delete-link" id="btn-delete">Supprimer le questionnaire</button>
        </div>

    </main>
    <div class="modal-blur-overlay" id="modal-success" style="display: none;">
            <div class="modal-card">
                <div>
                    <h3 class="modal-title">Sauvegarde réussie !</h3>
                    <p class="modal-desc">Les paramètres de votre questionnaire ont été enregistré avec succès.</p>
                </div>

                <div class="modal-actions">
                    <button class="btn-confirm" id="btn-confirm-success">OK</button>
                </div>
            </div>
        </div>

    <!-- DELETE MODAL -->
    <div class="modal-blur-overlay" id="modal-delete" style="display: none;">
        <div class="modal-card">
            <div>
                <h3 class="modal-title">Supprimer ce questionnaire ?</h3>
                <p class="modal-desc">Cette action est irréversible. Toutes les réponses associées seront perdues.</p>
            </div>

            <div class="modal-actions">
                <button class="btn-cancel" id="btn-cancel-delete">Annuler</button>
                <button class="btn-confirm btn-danger" id="btn-confirm-delete">Supprimer</button>
            </div>
        </div>
    </div>

    <script src="js/parametre.js?v=<?php echo time(); ?>"></script>
</body>

</html>