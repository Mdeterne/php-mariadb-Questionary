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
        :root {
            --primary: var(--red);
        }

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
            background-color: #fff0f0;
        }

        .option-radio {
            margin-right: 12px;
            accent-color: var(--primary);
            width: 20px;
            height: 20px;
        }

        /* Nouveaux styles pour les champs de saisie */
        .text-input,
        .text-area {
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

        .text-input:focus,
        .text-area:focus {
            border-color: var(--primary);
            outline: none;
        }

        /* Styles pour l'échelle */
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

        /* Styles de la modale */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.2s ease-out;
        }

        .modal-card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .modal-card h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.2rem;
            color: var(--dark);
        }

        .modal-card p {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #64748b;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
        }

        .btn-confirm {
            background: #252525;
            /* Explicit Blue */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-confirm:hover {
            background: #333;
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
                            <!-- Choix unique (Radio) -->
                            <div v-if="q.type === 'single_choice'" class="options-list">
                                <div v-for="opt in q.options" :key="opt.id">
                                    <label class="option-item" :class="{ selected: reponses[q.id] === opt.id }">
                                        <input type="radio" :name="'q_' + q.id" :value="opt.id" v-model="reponses[q.id]"
                                            class="option-radio">
                                        <span>{{ opt.label }}</span>
                                    </label>
                                    <!-- Champ texte si c'est une option ouverte et qu'elle est sélectionnée -->
                                    <div v-if="opt.is_open_ended == 1 && reponses[q.id] === opt.id"
                                        style="margin-left: 36px; margin-top: 8px;">
                                        <input type="text" v-model="autreReponses[q.id]" class="text-input"
                                            placeholder="Veuillez préciser...">
                                    </div>
                                </div>
                            </div>

                            <!-- Choix multiples (Checkbox) -->
                            <div v-else-if="q.type === 'multiple_choice'" class="options-list">
                                <div v-for="opt in q.options" :key="opt.id">
                                    <label class="option-item"
                                        :class="{ selected: Array.isArray(reponses[q.id]) && reponses[q.id].includes(opt.id) }">
                                        <input type="checkbox" :name="'q_' + q.id" :value="opt.id"
                                            v-model="reponses[q.id]" class="option-checkbox">
                                        <span>{{ opt.label }}</span>
                                    </label>
                                    <!-- Champ texte si c'est une option ouverte et qu'elle est cochée -->
                                    <div v-if="opt.is_open_ended == 1 && Array.isArray(reponses[q.id]) && reponses[q.id].includes(opt.id)"
                                        style="margin-left: 36px; margin-top: 8px;">
                                        <input type="text" v-model="autreReponses[q.id]" class="text-input"
                                            placeholder="Veuillez préciser...">
                                    </div>
                                </div>
                            </div>

                            <!-- Texte court -->
                            <div v-else-if="q.type === 'short_text'">
                                <input type="text" v-model="reponses[q.id]" class="text-input"
                                    placeholder="Votre réponse...">
                            </div>

                            <!-- Texte long (Paragraphe) -->
                            <div v-else-if="q.type === 'long_text'">
                                <textarea v-model="reponses[q.id]" @input="autoResize" class="text-area" rows="4"
                                    placeholder="Votre réponse..."></textarea>
                            </div>

                            <!-- Échelle (Jauge) -->
                            <div v-else-if="q.type === 'scale'" class="scale-container">
                                <div style="width: 100%; max-width: 90%; margin: 0 auto;">
                                    <input type="range" min="1" max="5" step="1" v-model="reponses[q.id]"
                                        style="width: 100%; margin-bottom: 8px; display: block; accent-color: var(--primary);">
                                    <div
                                        style="display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 500; color: #666; padding: 0 2px;">
                                        <span>1</span>
                                        <span>2</span>
                                        <span>3</span>
                                        <span>4</span>
                                        <span>5</span>
                                    </div>
                                    <div
                                        style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #999; margin-top: 5px;">
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

        <!-- Modale de confirmation personnalisée -->
        <div v-if="showModal" class="modal-overlay">
            <div class="modal-card">
                <h3>Confirmer l'envoi</h3>
                <p>Êtes-vous sûr de vouloir envoyer vos réponses ?<br>Cette action est définitive.</p>
                <div class="modal-buttons">
                    <button @click="showModal = false" class="btn-cancel">Annuler</button>
                    <button @click="confirmSubmission" class="btn-confirm">Oui, envoyer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vue.js 3 -->
    <script type="module">
        import { createApp } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js';

        createApp({
            data() {
                return {
                    loading: true,
                    showModal: false,
                    // Récupération des données du questionnaire (injectées via PHP)
                    questionnaire: {
                        title: "Titre du questionnaire",
                        description: "Description...",
                        questions: []
                    },
                    reponses: {}
                }
            },
            mounted() {
                // Chargement initial des données
                this.loadData();
            },
            methods: {
                loadData() {
                    const dataFromPHP = <?php echo json_encode($questionQuestionnaire ?? null); ?>;

                    if (dataFromPHP) {
                        this.questionnaire = dataFromPHP;
                        // Initialisation des réponses
                        this.questionnaire.questions.forEach(q => {
                            if (q.type === 'multiple_choice') {
                                this.reponses[q.id] = [];
                            } else {
                                this.reponses[q.id] = null;
                            }
                            // Initialisation du champ texte "Autre"
                            this.autreReponses[q.id] = '';
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
                submitAnswers() {
                    this.showModal = true;
                },
                async confirmSubmission() {
                    this.showModal = false;
                    this.loading = true; // Afficher le chargement

                    // Préparation des données pour inclure le texte "Autre" si nécessaire
                    const reponsesPayload = {};

                    for (const [qId, valeur] of Object.entries(this.reponses)) {
                        const question = this.questionnaire.questions.find(q => q.id == qId);
                        if (!question) continue;

                        // Gestion spéciale pour les questions avec option "Autre"
                        if (['single_choice', 'multiple_choice'].includes(question.type)) {
                            // Vérification de la présence d'une option "Réponse libre"
                            const optionsSelectionnees = [];
                            let aOptionAutreSelectionnee = false;

                            if (Array.isArray(valeur)) { // Checkbox
                                // Récupération des objets options complets correspondant aux IDs sélectionnés
                                const opts = question.options.filter(o => valeur.includes(o.id));
                                opts.forEach(o => {
                                    optionsSelectionnees.push(o.id);
                                    if (o.is_open_ended == 1) aOptionAutreSelectionnee = true;
                                });
                            } else { // Radio
                                const opt = question.options.find(o => o.id == valeur);
                                if (opt) {
                                    optionsSelectionnees.push(opt.id);
                                    if (opt.is_open_ended == 1) aOptionAutreSelectionnee = true;
                                }
                            }

                            if (aOptionAutreSelectionnee && this.autreReponses[qId]) {
                                // Structuration de la réponse pour le traitement backend
                                reponsesPayload[qId] = {
                                    options: optionsSelectionnees,
                                    text_value: this.autreReponses[qId]
                                };
                            } else {
                                // Format de réponse standard (sans texte additionnel)
                                reponsesPayload[qId] = valeur;
                            }
                        } else {
                            // Questions texte standard
                            reponsesPayload[qId] = valeur;
                        }
                    }

                    try {
                        const response = await fetch('index.php?c=home&a=saveReponse', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                survey_id: this.questionnaire.id,
                                answers: reponsesPayload
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            window.location.href = "index.php?c=home&a=merci";
                        } else {
                            alert("Erreur lors de l'enregistrement : " + (result.error || "Erreur inconnue"));
                            this.loading = false;
                        }
                    } catch (error) {
                        console.error("Erreur réseau:", error);
                        alert("Une erreur est survenue lors de l'envoi.");
                        this.loading = false;
                    }
                }
            }
        }).mount('#app-student');
    </script>
</body>

</html>