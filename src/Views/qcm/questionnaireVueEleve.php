<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionnaire - Questionary</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .qcm-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .question-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.5s ease-out;
        }

        .question-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .options-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .option-item {
            display: flex;
            align-items: center;
            padding: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .option-item:hover {
            border-color: var(--primary);
            background-color: #f9fafb;
        }

        .option-item.selected {
            border-color: var(--primary);
            background-color: #eef2ff;
        }

        .option-radio {
            margin-right: 12px;
            accent-color: var(--primary);
            width: 20px;
            height: 20px;
        }

        .submit-btn {
            background-color: var(--primary);
            color: white;
            padding: 16px 32px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            width: 100%;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background-color: var(--primary-dark);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../components/header.php'; ?>

    <div class="app-container" id="app-student">
        <main class="main-content qcm-container">

            <div v-if="loading" style="text-align:center; padding: 50px;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary);"></i>
                <p>Chargement du questionnaire...</p>
            </div>

            <div v-else>
                <div class="header-qcm" style="margin-bottom: 30px;">
                    <h1 style="font-size: 2rem; margin-bottom: 10px;">{{ questionnaire.titre }}</h1>
                    <p style="color: var(--gray);">{{ questionnaire.description }}</p>
                </div>

                <form @submit.prevent="submitAnswers">
                    <div v-for="(q, index) in questionnaire.questions" :key="q.id" class="question-card">
                        <div class="question-title">
                            <span style="color: var(--primary); margin-right: 8px;">{{ index + 1 }}.</span>
                            {{ q.texte }}
                        </div>

                        <div class="options-list">
                            <label v-for="opt in q.options" :key="opt.id" class="option-item"
                                :class="{ selected: reponses[q.id] === opt.id }">
                                <input type="radio" :name="'q_' + q.id" :value="opt.id" v-model="reponses[q.id]"
                                    class="option-radio">
                                <span>{{ opt.texte }}</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        Envoyer mes réponses <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i>
                    </button>
                </form>
            </div>

        </main>
    </div>

    <!-- Vue.js 3 -->
    <script type="module">
        import { createApp } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js';

        createApp({
            data() {
                return {
                    loading: true,
                    // Récupération des données PHP injectées ou mock
                    questionnaire: {
                        titre: "Titre du questionnaire",
                        description: "Description...",
                        questions: []
                    },
                    reponses: {}
                }
            },
            mounted() {
                // Simulation de chargement de données (réelles ou injectées)
                this.loadData();
            },
            methods: {
                loadData() {
                    // Ici on récupèrerait les données passées par PHP via une variable globale
                    // EXEMPLE : const dataFromPHP = <?php echo json_encode($questionQuestionnaire ?? null); ?>;

                    // POUR LA DEMO FRONTEND : On utilise un Mock si PHP ne renvoie rien
                    const mockData = {
                        titre: "Culture Générale",
                        description: "Testez vos connaissances avec ce quiz rapide !",
                        questions: [
                            {
                                id: 101,
                                texte: "Quelle est la capitale de la France ?",
                                options: [
                                    { id: 1, texte: "Lyon" },
                                    { id: 2, texte: "Paris" },
                                    { id: 3, texte: "Marseille" },
                                    { id: 4, texte: "Bordeaux" }
                                ]
                            },
                            {
                                id: 102,
                                texte: "Combien font 2 + 2 ?",
                                options: [
                                    { id: 5, texte: "3" },
                                    { id: 6, texte: "4" },
                                    { id: 7, texte: "5" }
                                ]
                            },
                            {
                                id: 103,
                                texte: "Quel langage est utilisé pour le style web ?",
                                options: [
                                    { id: 8, texte: "HTML" },
                                    { id: 9, texte: "Python" },
                                    { id: 10, texte: "CSS" }
                                ]
                            }
                        ]
                    };

                    setTimeout(() => {
                        // Si on avait de la vraie donnée PHP, on l'utiliserait ici
                        this.questionnaire = mockData;
                        this.loading = false;
                    }, 500);
                },
                submitAnswers() {
                    console.log("Réponses envoyées :", this.reponses);
                    alert("Merci ! Vos réponses ont été enregistrées (Simulation).");
                    window.location.href = "index.php";
                }
            }
        }).mount('#app-student');
    </script>
</body>

</html>