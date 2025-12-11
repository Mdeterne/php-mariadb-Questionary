const { createApp } = Vue;
const draggable = window.vuedraggable;

const app = createApp({
    components: {
        draggable: draggable
    },
    data() {
        return {
            formTitle: '',
            formDescription: '',
            surveyId: null,
            questions: [],
            toolItems: [
                { type: 'Réponse courte', label: 'Réponse Courte', icon: 'fa-pen' },
                { type: 'Paragraphe', label: 'Paragraphe', icon: 'fa-align-left' },
                { type: 'Cases à cocher', label: 'Cases à cocher', icon: 'fa-square-check' },
                { type: 'Choix multiples', label: 'Choix multiples', icon: 'fa-circle-dot' },
                { type: 'Jauge', label: 'Jauge', icon: 'fa-sliders' }
            ],
            activeQuestionIndex: null,
            showSaveModal: false
        };
    },
    mounted() {
        if (window.existingSurvey) {
            const s = window.existingSurvey;
            this.surveyId = s.id;
            this.formTitle = s.title;
            this.formDescription = s.description;

            // Map backend questions to frontend
            if (s.questions && s.questions.length > 0) {
                this.questions = s.questions.map(q => {
                    let type = 'Réponse courte';
                    if (q.type === 'short_text') type = 'Réponse courte';
                    else if (q.type === 'long_text') type = 'Paragraphe';
                    else if (q.type === 'multiple_choice') type = 'Cases à cocher';
                    else if (q.type === 'single_choice') type = 'Choix multiples';
                    else if (q.type === 'scale') type = 'Jauge';

                    return {
                        id: Date.now() + Math.random(), // New temporary ID for vue loop
                        type: type,
                        title: q.label,
                        required: q.is_required == 1,
                        options: q.options ? q.options.map(o => ({ label: o.label })) : []
                    };
                });
            }
        }
    },
    methods: {
        addQuestion(type) {
            // Logic for manual add button if needed (though drag n drop is primary)
            this.questions.push(this.createQuestionObject(type));
        },
        createQuestionObject(type) {
            return {
                id: Date.now() + Math.random(),
                type: type,
                title: '',
                options: ['Cases à cocher', 'Choix multiples'].includes(type) ? [{ label: 'Option 1' }] : [],
                required: false
            };
        },
        removeQuestion(index) {
            this.questions.splice(index, 1);
        },
        addOption(question) {
            question.options.push({ label: `Option ${question.options.length + 1}` });
        },
        removeOption(question, index) {
            question.options.splice(index, 1);
        },
        saveForm() {
            const formData = new FormData();
            if (this.surveyId) {
                formData.append('id', this.surveyId);
            }
            formData.append('titre', this.formTitle);
            formData.append('description', this.formDescription);
            formData.append('questions', JSON.stringify(this.questions));

            console.log('Saving form:', this.formTitle, this.formDescription, this.questions);

            // Send to PHP backend
            fetch('?c=createur&a=save', {
                method: 'POST',
                body: formData,
            })
                .then(async response => {
                    if (!response.ok) {
                        const errText = await response.text();
                        throw new Error(errText || 'Erreur serveur');
                    }
                    return response.text();
                })
                .then(data => {
                    this.showSaveModal = true;
                    // alert('Questionnaire sauvegardé avec succès !');
                    // window.location.href = '?c=tableauDeBord'; // Redirection supprimée ici, faite après OK modal
                })
                .catch((error) => {
                    alert('Erreur lors de la sauvegarde : ' + error.message);
                });
        },
        closeSaveModal() {
            this.showSaveModal = false;
            window.location.href = '?c=tableauDeBord';
        },
        // Clone event for drag and drop
        onClone(evt) {
            const origEl = evt.item;
            // The clone operation is handled by VueDraggable's :group="{ pull: 'clone' }"
            // But we need to ensure the data pushed to 'questions' is a new object, not a reference to toolItems
            // VueDraggable handles this nicely usually, but lets verify if we need a custom clone function.
            // Yes, usually :clone="cloneDog" prop is needed on the source draggable.
        },
        cloneQuestion(toolItem) {
            // This function is called by draggable when cloning from toolbox
            return this.createQuestionObject(toolItem.type);
        },
        setActive(index) {
            this.activeQuestionIndex = index;
        }
    }
});

app.mount('#app');