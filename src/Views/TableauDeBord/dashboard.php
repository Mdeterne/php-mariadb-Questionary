<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace - Questionary</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/components/topbar.css">
    <link rel="stylesheet" href="css/components/buttons.css">
    <link rel="stylesheet" href="css/components/inputs.css">
    <link rel="stylesheet" href="css/components/cards.css">
    <link rel="stylesheet" href="css/components/sidebar.css">
    <link rel="stylesheet" href="css/components/footer.css">
    <link rel="stylesheet" href="css/components/modals.css">
    <link rel="stylesheet" href="css/pages/dashboard.css">
    <link rel="stylesheet" href="css/pages/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <?php require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Composants' . DIRECTORY_SEPARATOR . 'header.php'; ?>

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

        <main class="main-content" id="app-dashboard" v-cloak>

            <div class="top-tools">
                <div class="search-bar-container">
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" placeholder="Rechercher" class="search-input" v-model="termeRecherche">
                    </div>
                </div>

                <div class="user-profile" @click="toggleUserMenu" style="cursor: pointer; position: relative;">
                    <i class="fa-solid fa-circle-user"></i>
                    <div class="notification-badge" v-if="unreadNotificationsCount > 0">{{ unreadNotificationsCount }}
                    </div>
                </div>

                <div class="modal-overlay" v-if="showUserMenu" @click.self="toggleUserMenu">
                    <div class="user-modal-card">

                        <button class="modal-close-btn" @click="toggleUserMenu">
                            <i class="fa-solid fa-xmark"></i>
                        </button>

                        <div class="modal-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h3 class="modal-username"><?php echo $_SESSION['name'] ?? 'Utilisateur'; ?></h3>

                        <a href="?c=tableauDeBord&a=notifications" class="modal-logout-btn"
                            style="width: 100%; justify-content: center; margin-bottom: 5px;">
                            <i class="fa-solid fa-bell"></i> Notifications
                            <span v-if="unreadNotificationsCount > 0"
                                style="background: var(--red); color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7em; margin-left: 5px;">{{
                                unreadNotificationsCount }}</span>
                        </a>



                        <div class="modal-footer">
                            <a href="?c=home&a=conditionGenerales">Conditions générales</a> | <a
                                href="?c=home&a=confidentialite">Confidentialité</a>
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

                <div v-for="q in questionnairesFiltres" :key="q.id" style="position: relative;">
                    <a :href="'?c=createur&a=editer&id=' + q.id" style="text-decoration:none; color:inherit;">
                        <div class="card-q">
                            <div class="card-q-title">{{ q.titre }}</div>
                            <div class="card-q-qr" @click.prevent.stop="afficherQrCode(q.access_pin, q.titre)">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </div>
                        </div>
                    </a>
                </div>

            </section>

            <!-- MODALE D'IMPORTATION -->
            <div class="modal-blur-overlay" v-if="showImportModal" @click.self="showImportModal = false">
                <div class="modal-card">
                    <div>
                        <h3 class="modal-title">Importer un questionnaire</h3>
                        <p class="modal-desc">Entrez le code PIN du questionnaire que vous souhaitez ajouter à votre
                            espace.
                        </p>
                    </div>

                    <input type="text" class="modal-input" placeholder="Code PIN (ex: 123456)" v-model="lienImport"
                        @keyup.enter="validerImport">

                    <div class="modal-actions">
                        <button class="btn-cancel" @click="showImportModal = false">Annuler</button>
                        <button class="btn-confirm" @click="validerImport">Importer</button>
                    </div>
                </div>
            </div>

            <!-- MODALE DE SUCCÈS D'IMPORTATION -->
            <div class="modal-blur-overlay" v-if="showImportSuccess" @click.self="showImportSuccess = false">
                <div class="modal-card">
                    <div>
                        <h3 class="modal-title">Importation réussie !</h3>
                        <p class="modal-desc" style="margin-top: 10px;">
                            Le questionnaire a été importé avec succès. Vous pouvez maintenant le retrouver dans votre
                            tableau de bord.
                        </p>
                    </div>

                    <div class="modal-actions">
                        <button class="btn-confirm" @click="showImportSuccess = false">OK</button>
                    </div>
                </div>
            </div>

            <!-- MODALE D'ERREUR D'IMPORTATION -->
            <div class="modal-blur-overlay" v-if="showImportError" @click.self="showImportError = false">
                <div class="modal-card">
                    <div>
                        <h3 class="modal-title">Importation impossible</h3>
                        <p class="modal-desc" style="margin-top: 10px;">
                            Le questionnaire n'a pas pu être importé. Vérifiez le code PIN ou assurez-vous que vous
                            n'êtes pas déjà le propriétaire.
                        </p>
                    </div>

                    <div class="modal-actions">
                        <button class="btn-confirm" @click="showImportError = false">OK</button>
                    </div>
                </div>
            </div>

            <!-- MODALE DE CONFIRMATION DE SUPPRESSION -->
            <div class="modal-blur-overlay" v-if="questionnaireToDelete" @click.self="annulerSuppression">
                <div class="modal-card">
                    <div>
                        <h3 class="modal-title">Supprimer ce questionnaire ?</h3>
                        <p class="modal-desc">Cette action est irréversible. Toutes les réponses associées seront
                            perdues.</p>
                    </div>

                    <div class="modal-actions">
                        <button class="btn-cancel" @click="annulerSuppression">Annuler</button>
                        <button class="btn-confirm btn-danger" @click="confirmerSuppression">Supprimer</button>
                    </div>
                </div>
            </div>

            <!-- MODALE QR CODE -->
            <div class="modal-blur-overlay" v-if="showQrModal" @click.self="closeQrModal">
                <div class="modal-card" style="text-align: center;">
                    <button class="modal-close-btn" @click="closeQrModal">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    <h3 class="modal-title" style="margin-bottom: 5px;">Scannez pour répondre</h3>
                    <h2 style="font-size: 2.5rem; font-weight: 800; color: #333; margin: 10px 0;">{{ qrPin }}</h2>
                    <h4 style="margin-bottom: 20px; color: #666; font-weight: normal;">{{ qrTitle }}</h4>

                    <div
                        style="background: white; padding: 20px; display: inline-block; border-radius: 10px; border: 1px solid #eee;">
                        <qrcode-vue :value="qrLink" :size="200" level="H"></qrcode-vue>
                    </div>



                    <div class="modal-actions" style="justify-content: center; margin-top: 20px;">
                        <button class="btn-confirm" @click="downloadQrImage"> <i class="fa-solid fa-download"></i>
                            Télécharger l'image</button>
                        <button class="btn-cancel" @click="closeQrModal" style="margin-left: 10px;">Fermer</button>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script>
        window.serverQuestionnaires = <?php echo json_encode($mesQuestionnaires ?? []) ?: '[]'; ?>;
        // Mock notifications for now
        window.serverNotifications = [
            { id: 1, message: "LOI Milan a repondu au questionnaire : Questionnaire 1", read: false }
        ];
    </script>
    <script type="importmap">
    {
        "imports": {
            "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js",
            "qrcode.vue": "https://cdn.jsdelivr.net/npm/qrcode.vue@3.6.0/dist/qrcode.vue.esm.js"
        }
    }
    </script>
    <script type="module" src="js/dashboard-app.js"></script>

</body>

</html>