<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditeur - Questionary</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <header class="topbar">
        <div class="topbar__left">
            <a href="?c=tableauDeBord" class="topbar__logo">
                <span class="appicon" aria-hidden="true"></span>
                <span class="apptitle">QUESTIONARY</span>
            </a>
        </div>

        <div class="topbar__right" aria-label="Université de Limoges">
            <span class="uni-badge" aria-hidden="true">uℓ</span>
            <span class="uni-text">Université de Limoges</span>
        </div>
    </header>

    <div id="app" class="editor-container">

        <aside class="editor-toolbox">
            <!-- Draggable Source Group -->
            <draggable class="toolbox-list" :list="toolItems" :group="{ name: 'questions', pull: 'clone', put: false }"
                :clone="cloneQuestion" :sort="false" item-key="type">
                <template #item="{ element }">
                    <div class="tool-item" @click="addQuestion(element.type)">
                        <i :class="['fa-solid', element.icon]"></i> {{ element.label }}
                    </div>
                </template>
            </draggable>
        </aside>

        <main class="editor-workspace">

            
            <div class="question-card form-title-card">
                <input type="text" class="input-field title-input" placeholder="Titre du formulaire">
                <input type="text" class="input-field desc-input" placeholder="Description du formulaire">
            </div>
            

            <!-- Drop Zone / Canvas -->
            <draggable class="zone-depot" v-model="questions" group="questions" item-key="id" handle=".drag-handle"
                ghost-class="ghost">

                <template #item="{ element, index }">
                    <div class="question-card" :class="{ 'active-card': activeQuestionIndex === index }"
                        @click="setActive(index)">

                        <div class="question-header">
                            <span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span>
                            <div class="question-type-badge">{{ element.type }}</div>
                            <button class="btn-icon delete" @click.stop="removeQuestion(index)"><i
                                    class="fa-solid fa-trash"></i></button>
                        </div>

                        <div class="question-body">
                            <input type="text" class="input-field question-title" v-model="element.title"
                                placeholder="Question sans titre">

                            <!-- Dynamic Inputs based on type -->
                            <div v-if="element.type === 'Réponse courte'" class="preview-input">
                                <input disabled type="text" placeholder="Réponse courte">
                            </div>

                            <div v-if="element.type === 'Paragraphe'" class="preview-input">
                                <textarea disabled class="area-drop" placeholder="Réponse longue"></textarea>
                            </div>

                            <div v-if="['Cases à cocher', 'Choix multiples'].includes(element.type)">
                                <div v-for="(opt, optIndex) in element.options" :key="optIndex" class="option-row">
                                    <i
                                        :class="element.type === 'Cases à cocher' ? 'fa-regular fa-square' : 'fa-regular fa-circle'"></i>
                                    <input type="text" v-model="opt.label" placeholder="Option">
                                    <button @click="removeOption(element, optIndex)" class="btn-icon"><i
                                            class="fa-solid fa-xmark"></i></button>
                                </div>
                                <button @click="addOption(element)" class="btn-text">+ Ajouter une option</button>
                            </div>

                            <div v-if="element.type === 'Jauge'" class="preview-input">
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
                                </div>
                            </div>

                            <!-- Actions Bar (Floating) for Active Card -->
                            <div class="question-actions" v-if="activeQuestionIndex === index">
                                <!-- Future actions like duplicate, required toggle etc -->
                            </div>
                        </div>

                    </div>
                </template>

                <template #footer>
                    <div class="empty-placeholder">
                        <i class="fa-solid fa-cloud-arrow-down placeholder-icon"></i>
                        <span class="placeholder-text">Glissez des éléments ici</span>
                    </div>
                </template>
            </draggable>
        </main>

        <aside class="editor-actions">
            <a href="?c=createur&a=save">
                <button class="btn-action save">Sauvegarder</button>
            </a>
           <a href="index.php?c=tableauDeBord&a=parametres">
                <button class="btn-action settings">Paramètres</button>
            </a>
            <a href="?c=tableauDeBord">
                <button class="btn-action quit">Quitter</button>
            </a>

        </aside>

    </div>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://unpkg.com/vuedraggable@4.1.0/dist/vuedraggable.umd.min.js"></script>

    <!-- App Logic -->
    <script src="js/creation_questionnaire.js"></script>

</body>

</html>