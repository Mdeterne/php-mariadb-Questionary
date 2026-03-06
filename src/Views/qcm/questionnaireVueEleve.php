<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionnaire - Questionary</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/pages/questionnaire_eleve.css">
</head>

<body>
    <?php
    $logoHref = 'javascript:void(0)';
    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Composants' . DIRECTORY_SEPARATOR . 'header.php';
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
                    <div v-for="(q, index) in questionnaire.questions" :key="q.id" class="question-card"
                        v-show="isQuestionVisible(q)">
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
                    reponses: {},
                    autreReponses: {}
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
                isQuestionVisible(q) {
                    if (!q.parent_question_id) return true;

                    const parentId = q.parent_question_id;
                    const requiredLabel = q.parent_option_label;
                    const parentAnswer = this.reponses[parentId];

                    if (!parentAnswer) return false;

                    const parentQ = this.questionnaire.questions.find(pq => pq.id == parentId);
                    if (!parentQ) return false;

                    if (Array.isArray(parentAnswer)) {
                        // Checkbox: parentAnswer est un tableau d'IDs d'options
                        const selectedOptions = parentQ.options.filter(o => parentAnswer.includes(o.id));
                        return selectedOptions.some(o => o.label === requiredLabel);
                    } else {
                        // Radio (ou autre): parentAnswer est un ID
                        const opt = parentQ.options.find(o => o.id == parentAnswer);
                        return opt && opt.label === requiredLabel;
                    }
                },
                autoResize(event) {
                    const textarea = event.target;
                    textarea.style.height = 'auto';
                    textarea.style.height = textarea.scrollHeight + 'px';
                },
                submitAnswers() {
                    // Bypass modal due to visibility issues
                    this.confirmSubmission();
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