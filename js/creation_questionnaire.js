const { createApp } = Vue;
const draggable = window.vuedraggable;

const app = createApp({
    components: {
        draggable: draggable
    },
    data() {
        return {
            formTitle: '',
            questions: [],
            toolItems: [
                { type: 'Réponse courte', label: 'Réponse Courte', icon: 'fa-pen' },
                { type: 'Paragraphe', label: 'Paragraphe', icon: 'fa-align-left' },
                { type: 'Cases à cocher', label: 'Cases à cocher', icon: 'fa-square-check' },
                { type: 'Choix multiples', label: 'Choix multiples', icon: 'fa-circle-dot' },
                { type: 'Jauge', label: 'Jauge', icon: 'fa-sliders' }
            ],
            activeQuestionIndex: null
        };
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
            const formData = {
                title: this.formTitle,
                questions: this.questions
            };
            console.log('Saving form:', formData);

            // Send to PHP backend
            fetch('?c=questionnaire&a=save', { // Placeholder URL
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            })
                .then(response => response.json())
                .then(data => {
                    alert('Questionnaire sauvegardé (Simulation)');
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('Sauvegarde simulée');
                });
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