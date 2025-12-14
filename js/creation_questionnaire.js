const { createApp } = Vue;
const draggable = window.vuedraggable;

const app = createApp({
    components: {
        draggable: draggable
    },
    data() {
        return {
            titreFormulaire: '',
            descriptionFormulaire: '',
            idQuestionnaire: null,
            questions: [],
            outils: [
                { type: 'Réponse courte', label: 'Réponse Courte', icon: 'fa-pen' },
                { type: 'Paragraphe', label: 'Paragraphe', icon: 'fa-align-left' },
                { type: 'Cases à cocher', label: 'Cases à cocher', icon: 'fa-square-check' },
                { type: 'Choix multiples', label: 'Choix multiples', icon: 'fa-circle-dot' },
                { type: 'Jauge', label: 'Jauge', icon: 'fa-sliders' }
            ],
            indexQuestionActive: null,
            afficherModaleSauvegarde: false
        };
    },
    mounted() {
        if (window.existingSurvey) {
            const s = window.existingSurvey;
            this.idQuestionnaire = s.id;
            this.titreFormulaire = s.title;
            this.descriptionFormulaire = s.description;

            // Adaptation des questions pour le frontend
            if (s.questions && s.questions.length > 0) {
                this.questions = s.questions.map(q => {
                    let type = 'Réponse courte';
                    if (q.type === 'short_text') type = 'Réponse courte';
                    else if (q.type === 'long_text') type = 'Paragraphe';
                    else if (q.type === 'multiple_choice') type = 'Cases à cocher';
                    else if (q.type === 'single_choice') type = 'Choix multiples';
                    else if (q.type === 'scale') type = 'Jauge';

                    return {
                        id: Date.now() + Math.random(), // Id temporaire pour la boucle vue
                        type: type,
                        title: q.label,
                        required: q.is_required == 1,
                        options: q.options ? q.options.map(o => ({ label: o.label })) : [],
                        scale_min_label: q.scale_min_label || 'Pas du tout',
                        scale_max_label: q.scale_max_label || 'Tout à fait'
                    };
                });
            }
        }
    },
    methods: {
        ajouterQuestion(type) {
            // Logique pour le bouton d'ajout manuel
            this.questions.push(this.creerObjetQuestion(type));
        },
        creerObjetQuestion(type) {
            return {
                id: Date.now() + Math.random(),
                type: type,
                title: '',
                options: ['Cases à cocher', 'Choix multiples'].includes(type) ? [{ label: 'Option 1' }] : [],
                required: false,
                scale_min_label: type === 'Jauge' ? 'Pas du tout' : '',
                scale_max_label: type === 'Jauge' ? 'Tout à fait' : ''
            };
        },
        supprimerQuestion(index) {
            this.questions.splice(index, 1);
        },
        ajouterOption(question) {
            question.options.push({ label: `Option ${question.options.length + 1}` });
        },
        supprimerOption(question, index) {
            question.options.splice(index, 1);
        },
        sauvegarderFormulaire() {
            const donneesFormulaire = new FormData();
            if (this.idQuestionnaire) {
                donneesFormulaire.append('id', this.idQuestionnaire);
            }
            donneesFormulaire.append('titre', this.titreFormulaire);
            donneesFormulaire.append('description', this.descriptionFormulaire);
            donneesFormulaire.append('questions', JSON.stringify(this.questions));

            console.log('Sauvegarde du formulaire :', this.titreFormulaire, this.descriptionFormulaire, this.questions);

            // Envoi au backend
            fetch('?c=createur&a=save', {
                method: 'POST',
                body: donneesFormulaire,
            })
                .then(async reponse => {
                    if (!reponse.ok) {
                        const texteErreur = await reponse.text();
                        throw new Error(texteErreur || 'Erreur serveur');
                    }
                    return reponse.text();
                })
                .then(donnees => {
                    this.afficherModaleSauvegarde = true;
                })
                .catch((erreur) => {
                    alert('Erreur lors de la sauvegarde : ' + erreur.message);
                });
        },
        fermerModaleSauvegarde() {
            this.afficherModaleSauvegarde = false;
            window.location.href = '?c=tableauDeBord';
        },
        clonerQuestion(outil) {
            // Fonction utiliser par draggable lors du clonage depuis la boîte à outils
            return this.creerObjetQuestion(outil.type);
        },
        definirActif(index) {
            this.indexQuestionActive = index;
        },
        allerAuxParametres() {
            if (this.idQuestionnaire) {
                window.location.href = `index.php?c=tableauDeBord&a=parametres&id=${this.idQuestionnaire}`;
            } else {
                alert("Veuillez d'abord sauvegarder le questionnaire pour accéder aux paramètres.");
            }
        }
    }
});

app.mount('#app');