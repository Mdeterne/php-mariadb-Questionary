<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Questionary</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/components/topbar.css">
    <link rel="stylesheet" href="css/components/buttons.css">
    <link rel="stylesheet" href="css/components/sidebar.css">
    <link rel="stylesheet" href="css/pages/dashboard.css">
    <link rel="stylesheet" href="css/pages/notifications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <?php require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Composants' . DIRECTORY_SEPARATOR . 'header.php'; ?>

    <div class="conteneur-page-notifications">

        <div class="conteneur-lien-retour">
            <a href="?c=tableauDeBord" class="btn-marquer-lu lien-retour">
                <i class="fa-solid fa-arrow-left"></i> Retour au tableau de bord
            </a>
        </div>

        <main id="app-notifications" v-cloak>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="section-title" style="margin-bottom: 0;">Notifications</h2>
                <button v-if="aDesNonLues" @click="marquerToutesLues" class="btn-marquer-lu"
                    style="background-color: #f1f1f1; color: #333;">
                    <i class="fa-solid fa-check-double"></i> Tout marquer comme lu
                </button>
            </div>

            <div class="conteneur-notifications">
                <div v-if="notifications.length === 0" class="notifications-vides">
                    Aucune notification.
                </div>

                <div v-for="notification in notifications" :key="notification.id" class="carte-notification"
                    :class="{ 'non-lu': !notification.read }">
                    <div class="contenu-notification">
                        {{ notification.message }}
                    </div>
                    <span v-if="notification.read" class="statut-lecture">
                        <i class="fa-solid fa-check"></i> Lu
                    </span>
                </div>
            </div>

        </main>
    </div>

    <script>
        window.notificationsServeur = <?php echo json_encode($notifications ?? []) ?: '[]'; ?>;
    </script>
    <script type="importmap">
    {
        "imports": {
            "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
        }
    }
    </script>
    <script type="module">
        import { createApp } from 'vue';

        createApp({
            data() {
                return {
                    notifications: window.notificationsServeur || []
                }
            },
            computed: {
                aDesNonLues() {
                    return this.notifications.some(n => !n.read);
                }
            },
            methods: {
                async marquerLu(notification) {
                    try {
                        const reponse = await fetch('?c=tableauDeBord&a=marquerNotificationLue', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ id: notification.id })
                        });

                        const resultat = await reponse.json();

                        if (resultat.success) {
                            notification.read = true;
                        } else {
                            console.error("Erreur serveur:", resultat.error);
                        }
                    } catch (erreur) {
                        console.error("Erreur réseau:", erreur);
                    }
                },
                async marquerToutesLues() {
                    try {
                        const reponse = await fetch('?c=tableauDeBord&a=marquerToutesNotificationsLues', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' }
                        });

                        const resultat = await reponse.json();

                        if (resultat.success) {
                            this.notifications.forEach(n => n.read = true);
                        } else {
                            alert("Erreur : " + resultat.error);
                        }
                    } catch (erreur) {
                        console.error("Erreur réseau:", erreur);
                        alert("Impossible de contacter le serveur.");
                    }
                }
            }
        }).mount('#app-notifications');
    </script>

</body>

</html>