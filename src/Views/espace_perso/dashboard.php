<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace - Questionary</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <?php require_once __DIR__ . '/../components/header.php'; ?>

    <div class="app-container">

        <aside class="sidebar">
            <div class="sidebar-header-box">
                Résultats
            </div>
            <nav class="sidebar-nav">
                <?php if (empty($mesQuestionnaires)): ?>
                    <p style="color: #aaa; font-size: 0.8rem; padding: 10px;">Aucun résultat récent.</p>
                <?php else: ?>
                    <?php foreach ($mesQuestionnaires as $q): ?>
                        <a href="?c=espaceAnalyse&id=<?= htmlspecialchars($q['id']) ?>">
                            <span class="sidebar-link-text"><?= htmlspecialchars($q['titre']) ?></span>
                            <i class="fa-solid fa-chart-pie"></i>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav>
        </aside>

        <main class="main-content" id="app-dashboard">

            <div class="top-tools">
                <div class="search-bar-container">
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" placeholder="Rechercher" class="search-input" v-model="termeRecherche">
                    </div>
                </div>

                <div class="user-profile" @click="toggleUserMenu" style="cursor: pointer;">
                    <i class="fa-solid fa-circle-user"></i>
                </div>

                <div class="modal-overlay" v-if="showUserMenu" @click.self="toggleUserMenu">
                    <div class="user-modal-card">

                        <button class="modal-close-btn" @click="toggleUserMenu">
                            <i class="fa-solid fa-xmark"></i>
                        </button>

                        <div class="modal-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h3 class="modal-username">Nom d'utilisateur</h3>

                        <a href="?c=connexion&a=deconnecter" class="modal-logout-btn">
                            Se déconnecter <i class="fa-solid fa-right-from-bracket"></i>
                        </a>

                        <div class="modal-footer">
                            <a href="?c=tableauDeBord&a=conditionGenerales">Conditions générales</a> | <a
                                href="?c=tableauDeBord&a=confidentialite">Confidentialité</a>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="section-title">Créer un questionnaire</h2>
            <section class="create-section">
                <a href="?c=createur&a=nouveauFormulaire">
                    <div class="card-create" @click="creerNouveau">
                        <div class="btn-plus">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                    </div>
                </a>

                <div class="card-create" @click="showImportModal = true">
                    <div class="btn-import">Importer</div>
                </div>

            </section>

            <h2 class="section-title">Vos Questionnaires</h2>
            <section class="questionnaire-grid">

                <div v-if="!questionnairesFiltres || questionnairesFiltres.length === 0"
                    style="grid-column: 1 / -1; color: #888;">
                    Vous n'avez pas encore créé de questionnaire.
                </div>

                <div v-for="q in questionnairesFiltres" :key="q.id" class="card-q">
                    <div class="card-q-title">{{ q.titre }}</div>
                    <div class="card-q-qr">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <i @click="supprimer(q.id)" class="fa-solid fa-trash"
                        style="cursor:pointer; color: var(--red);"></i>
                </div>

            </section>

            <div class="modal-blur-overlay" v-if="showImportModal" @click.self="showImportModal = false">
                <div class="import-card">

                    <div>
                        <h3 class="import-title">Importer un questionnaire</h3>
                        <p class="import-desc">Collez le lien du questionnaire que vous souhaitez ajouter à votre
                            espace.</p>
                    </div>

                    <input type="text" class="input-import" placeholder="https://questionary.app/..."
                        v-model="lienImport" @keyup.enter="validerImport">

                    <div class="modal-actions">
                        <button class="btn-cancel" @click="showImportModal = false">Annuler</button>
                        <button class="btn-confirm-import" @click="validerImport">Importer</button>
                    </div>

                </div>
            </div>

        </main>
    </div>

    <script>
        window.serverQuestionnaires = <?php echo json_encode($mesQuestionnaires ?? []) ?: '[]'; ?>;
    </script>
    <script type="module" src="js/dashboard-app.js"></script>

</body>

</html>