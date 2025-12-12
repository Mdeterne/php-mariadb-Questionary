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

        /* Submit button replaced by .btn .btn-primary */

        /* New Styles for Inputs */
        .text-input, .text-area {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .text-area {
            resize: none;
            overflow-y: hidden;
            min-height: 100px;
        }
        .text-input:focus, .text-area:focus {
            border-color: var(--primary);
            outline: none;
        }
        /* Scale styles removed (replaced by standard range input) */
        .option-checkbox {
            margin-right: 10px;
            accent-color: var(--primary);
            width: 18px;
            height: 18px;
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
    <?php 
    $logoHref = 'javascript:void(0)';
    require_once __DIR__ . '/../components/header.php'; 
    ?>

    <div class="app-container" id="app-student">
        <main class="main-content qcm-container">

            <div v-if="loading" style="text-align:center; padding: 50px;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary);"></i>
                <p>Chargement du questionnaire...</p>
            </div>

            <div v-else>
                <div class="header-qcm" style="margin-bottom: 30px;">
                    <h1 style="font-size: 2rem; margin-bottom: 10px;">{{ questionnaire.title }}</h1>
                    <p style="color: var(--gray);">{{ questionnaire.description }}</p>
                </div>

                <form @submit.prevent="submitAnswers">
                    <div v-for="(q, index) in questionnaire.questions" :key="q.id" class="question-card">
                        <div class="question-title">
                            <span style="color: var(--primary); margin-right: 8px;">{{ index + 1 }}.</span>
                            {{ q.label }}
                        </div>

                        <div class="answer-area">
                            <!-- Single Choice -->
                            <div v-if="q.type === 'single_choice'" class="options-list">
                                <label v-for="opt in q.options" :key="opt.id" class="option-item"
                                    :class="{ selected: reponses[q.id] === opt.id }">
                                    <input type="radio" :name="'q_' + q.id" :value="opt.id" v-model="reponses[q.id]" class="option-radio">
                                    <span>{{ opt.label }}</span>
                                </label>
                            </div>

                            <!-- Multiple Choice -->
                            <div v-else-if="q.type === 'multiple_choice'" class="options-list">
                                <label v-for="opt in q.options" :key="opt.id" class="option-item"
                                    :class="{ selected: Array.isArray(reponses[q.id]) && reponses[q.id].includes(opt.id) }">
                                    <input type="checkbox" :name="'q_' + q.id" :value="opt.id" v-model="reponses[q.id]" class="option-checkbox">
                                    <span>{{ opt.label }}</span>
                                </label>
                            </div>

                            <!-- Short Text -->
                            <div v-else-if="q.type === 'short_text'">
                                <input type="text" v-model="reponses[q.id]" class="text-input" placeholder="Votre réponse...">
                            </div>

                            <!-- Long Text -->
                            <div v-else-if="q.type === 'long_text'">
                                <textarea v-model="reponses[q.id]" @input="autoResize" class="text-area" rows="4" placeholder="Votre réponse..."></textarea>
                            </div>

                            <!-- Scale -->
                            <div v-else-if="q.type === 'scale'" class="scale-container">
                                <div style="width: 100%; max-width: 90%; margin: 0 auto;">
                                    <input type="range" min="1" max="5" step="1" v-model="reponses[q.id]" style="width: 100%; margin-bottom: 8px; display: block; accent-color: var(--primary);">
                                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 500; color: #666; padding: 0 2px;">
                                        <span>1</span>
                                        <span>2</span>
                                        <span>3</span>
                                        <span>4</span>
                                        <span>5</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #999; margin-top: 5px;">
                                        <span>{{ q.scale_min_label || 'Pas du tout' }}</span>
                                        <span>{{ q.scale_max_label || 'Tout à fait' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 30px;">
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
                        title: "Titre du questionnaire",
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
                    const dataFromPHP = <?php echo json_encode($questionQuestionnaire ?? null); ?>;
                    
                    if (dataFromPHP) {
                        this.questionnaire = dataFromPHP;
                        // Initialize responses
                        this.questionnaire.questions.forEach(q => {
                            if (q.type === 'multiple_choice') {
                                this.reponses[q.id] = [];
                            } else {
                                this.reponses[q.id] = null;
                            }
                        });
                        this.loading = false;
                    } else {
                        console.error("Aucune donnée reçue de PHP");
                        this.loading = false;
                    }
                },
                autoResize(event) {
                    const textarea = event.target;
                    textarea.style.height = 'auto';
                    textarea.style.height = textarea.scrollHeight + 'px';
                },
                async submitAnswers() {
                    if (confirm("Voulez-vous vraiment envoyer vos réponses ?")) {
                        try {
                            const response = await fetch('index.php?c=home&a=saveReponse', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    survey_id: this.questionnaire.id,
                                    answers: this.reponses
                                })
                            });

                            const result = await response.json();

                            if (result.success) {
                                alert("Merci ! Vos réponses ont été enregistrées.");
                                window.location.href = "index.php";
                            } else {
                                alert("Erreur lors de l'enregistrement : " + (result.error || "Erreur inconnue"));
                            }
                        } catch (error) {
                            console.error("Erreur réseau:", error);
                            alert("Une erreur est survenue lors de l'envoi.");
                        }
                    }
                }
            }
        }).mount('#app-student');
    </script>
</body>

</html>