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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .notifications-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .notif-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notif-card.unread {
            border-left: 5px solid var(--red);
            background-color: #fff9f9;
        }

        .notif-content {
            font-size: 1rem;
            color: #333;
        }

        .btn-read {
            background: none;
            border: 1px solid #ddd;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
            color: #666;
            transition: all 0.2s;
        }

        .btn-read:hover {
            background: #f5f5f5;
        }
    </style>
</head>

<body>

    <?php require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Composants' . DIRECTORY_SEPARATOR . 'header.php'; ?>

    <div style="max-width: 900px; margin: 40px auto; padding: 0 20px;">

        <div style="margin-bottom: 20px;">
            <a href="?c=tableauDeBord" class="btn-read"
                style="text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-left"></i> Retour au tableau de bord
            </a>
        </div>

        <main id="app-notifications" v-cloak>
            <h2 class="section-title">Notifications</h2>

            <div class="notifications-container">
                <div v-if="notifications.length === 0" style="color: #888; text-align: center; margin-top: 50px;">
                    Aucune notification.
                </div>

                <div v-for="notif in notifications" :key="notif.id" class="notif-card"
                    :class="{ 'unread': !notif.read }">
                    <div class="notif-content">
                        {{ notif.message }}
                    </div>
                    <button class="btn-read" @click="marquerLu(notif)" v-if="!notif.read">
                        Marquer comme lu
                    </button>
                    <span v-else style="color: green; font-size: 0.8rem;">
                        <i class="fa-solid fa-check"></i> Lu
                    </span>
                </div>
            </div>

        </main>
    </div>

    <script>
        window.serverNotifications = <?php echo json_encode($notifications ?? []) ?: '[]'; ?>;
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
                    notifications: window.serverNotifications || []
                }
            },
            methods: {
                marquerLu(notif) {
                    notif.read = true;
                    // Mock server call
                    console.log("Marqué comme lu:", notif.id);
                }
            }
        }).mount('#app-notifications');
    </script>

</body>

</html>