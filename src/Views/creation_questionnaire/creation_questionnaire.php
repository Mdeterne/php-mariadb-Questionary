<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditeur - Questionary</title>

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/components/topbar.css">
    <link rel="stylesheet" href="css/components/buttons.css">
    <link rel="stylesheet" href="css/components/inputs.css">
    <link rel="stylesheet" href="css/components/cards.css">
    <link rel="stylesheet" href="css/components/sidebar.css">
    <link rel="stylesheet" href="css/components/modals.css">
    <link rel="stylesheet" href="css/pages/editor.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <?php require_once __DIR__ . '/../components/header.php'; ?>

    <div id="app" class="editor-container" v-cloak>

        <aside class="editor-toolbox">
            <div class="sidebar-header-box">Éléments</div>
            <!-- Draggable Source Group -->
            <div class="toolbox-list sidebar-nav">
                <div class="tool-item" v-for="element in outils" :key="element.type"
                    @click="ajouterQuestion(element.type)">
                    <i :class="['fa-solid', element.icon]"></i> {{ element.label }}
                </div>
            </div>
        </aside>

        <main class="editor-workspace">


            <div class="question-card form-title-card">
                <input type="text" class="input-field title-input" v-model="titreFormulaire"
                    placeholder="Titre du formulaire">
                <input type="text" class="input-field desc-input" v-model="descriptionFormulaire"
                    placeholder="Description du formulaire">
            </div>


            <!-- Zone de depot et canvas -->
            <div class="zone-depot">
                <div v-if="questions.length === 0" class="empty-placeholder">
                    <i class="fa-solid fa-cloud-arrow-down placeholder-icon"></i>
                    <span class="placeholder-text">Cliquez sur un élément à gauche pour l'ajouter</span>
                </div>

                <div v-for="(element, index) in questions" :key="element.id" 
                     class="question-card" 
                     :class="{ 'active-card': indexQuestionActive === index }"
                     @click="definirActif(index)">

                    <div class="question-header">
                        <!-- Drag handle removed -->
                        <div class="question-type-badge">{{ element.type }}</div>
                        <button class="btn-icon delete" @click.stop="supprimerQuestion(index)">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>

                    <div class="question-body">
                        <input type="text" class="input-field question-title" v-model="element.title"
                            placeholder="Question sans titre">

                        <!-- Texte dynamique basé sur le type de question -->
                        <div v-if="element.type === 'Réponse courte'" class="preview-input">
                            <input disabled type="text" placeholder="Réponse courte">
                        </div>

                        <div v-if="element.type === 'Paragraphe'" class="preview-input">
                            <textarea disabled class="area-drop" placeholder="Réponse longue"></textarea>
                        </div>

                        <div v-if="['Cases à cocher', 'Choix multiples'].includes(element.type)">
                            <div v-for="(opt, optIndex) in element.options" :key="optIndex" class="option-row">
                                <i :class="element.type === 'Cases à cocher' ? 'fa-regular fa-square' : 'fa-regular fa-circle'"></i>

                                <!-- Affichage différent pour l'option Autre -->
                                <template v-if="opt.is_open_ended">
                                    <div style="flex:1; display:flex; gap:10px; align-items:center;">
                                        <span style="font-size: 0.9rem; color: #666;">Autre :</span>
                                        <input type="text" disabled placeholder="Réponse libre de l'utilisateur"
                                            style="border-style: dashed; background: #fafafa;">
                                    </div>
                                </template>
                                <template v-else>
                                    <input type="text" v-model="opt.label" placeholder="Option">
                                </template>

                                <button @click="supprimerOption(element, optIndex)" class="btn-icon"><i
                                        class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div style="display:flex; gap: 15px; margin-top: 10px;">
                                <button @click="ajouterOption(element)" class="btn-text">+ Ajouter une
                                    option</button>
                                <button v-if="!aUneOptionAutre(element)" @click="ajouterOptionAutre(element)"
                                    class="btn-text" style="color: #666;">+ Ajouter option "Autre"</button>
                            </div>
                        </div>

                        <div v-if="element.type === 'Jauge'" class="preview-input">
                            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                                <input type="text" v-model="element.scale_min_label"
                                    placeholder="Label Min (ex: Pas du tout)" class="input-field"
                                    style="font-size: 0.9rem;">
                                <input type="text" v-model="element.scale_max_label"
                                    placeholder="Label Max (ex: Tout à fait)" class="input-field"
                                    style="font-size: 0.9rem;">
                            </div>
                            <div style="width: 100%;">
                                <input type="range" min="1" max="5" value="3"
                                    style="width: 100%; margin-bottom: 8px; display: block;">
                                <div
                                    style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 500; color: var(--muted); padding: 0 2px;">
                                    <span>1</span>
                                    <span>2</span>
                                    <span>3</span>
                                    <span>4</span>
                                    <span>5</span>
                                </div>
                                <div
                                    style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #999; margin-top: 5px;">
                                    <span>{{ element.scale_min_label || 'Pas du tout' }}</span>
                                    <span>{{ element.scale_max_label || 'Tout à fait' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bar d'actions pour la question active -->
                        <div class="question-actions" v-if="indexQuestionActive === index">
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <aside class="editor-actions">
            <button class="btn-action save" @click="sauvegarderFormulaire">Sauvegarder</button>
            <button class="btn-action settings" @click="allerAuxParametres">Paramètres</button>
            <a href="?c=tableauDeBord">
                <button class="btn-action quit">Quitter</button>
            </a>

        </aside>

        <!-- Pop up de sauvegarde -->
        <div class="modal-blur-overlay" v-if="afficherModaleSauvegarde" @click.self="fermerModaleSauvegarde">
            <div class="modal-card">
                <div>
                    <h3 class="modal-title">Sauvegarde réussie !</h3>
                    <p class="modal-desc">Votre questionnaire a été enregistré avec succès.</p>
                </div>

                <div class="modal-actions">
                    <button class="btn-confirm" @click="fermerModaleSauvegarde">OK</button>
                </div>
            </div>
        </div>

    </div>
    <script>
        // Ajout des données existantes si elles sont disponibles
        window.existingSurvey = <?php echo isset($existingSurvey) ? json_encode($existingSurvey) : 'null'; ?>;
    </script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Logique de l'app -->
    <script src="js/creation_questionnaire.js"></script>

</body>

</html>