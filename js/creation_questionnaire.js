const { createApp } = Vue;

const app = createApp({
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
            afficherModaleSauvegarde: false,
            afficherModaleErreurTitre: false
        };
    },
    mounted() {
        if (window.existingSurvey) {
            const s = window.existingSurvey;
            this.idQuestionnaire = s.id;
            this.titreFormulaire = s.title;
            this.descriptionFormulaire = s.description;

            // Initialisation de l'état du composant des questions pour le frontend
            if (s.questions && s.questions.length > 0) {
                this.questions = s.questions.map(q => {
                    let type = 'Réponse courte';
                    if (q.type === 'short_text') type = 'Réponse courte';
                    else if (q.type === 'long_text') type = 'Paragraphe';
                    else if (q.type === 'multiple_choice') type = 'Cases à cocher';
                    else if (q.type === 'single_choice') type = 'Choix multiples';
                    else if (q.type === 'scale') type = 'Jauge';

                    return {
                        id: q.id || (Date.now() + Math.random()),
                        type: type,
                        title: q.label,
                        required: q.is_required == 1,
                        options: q.options ? q.options.map(o => ({
                            label: o.label,
                            is_open_ended: o.is_open_ended == 1
                        })) : [],
                        scale_min_label: q.scale_min_label || 'Pas du tout',
                        scale_max_label: q.scale_max_label || 'Tout à fait',
                        parent_question_id: q.parent_question_id || null,
                        parent_option_label: q.parent_option_label || null
                    };
                });
            }
        }
    },
    methods: {
        ajouterQuestion(type) {
            this.questions.push(this.creerObjetQuestion(type));
        },
        creerObjetQuestion(type) {
            return {
                id: Date.now() + Math.random(),
                type: type,
                title: '',
                options: ['Cases à cocher', 'Choix multiples'].includes(type) ? [{ label: 'Option 1', is_open_ended: false }] : [],
                required: false,
                scale_min_label: type === 'Jauge' ? 'Pas du tout' : '',
                scale_max_label: type === 'Jauge' ? 'Tout à fait' : '',
                parent_question_id: null,
                parent_option_label: null
            };
        },
        supprimerQuestion(index) {
            this.questions.splice(index, 1);
        },
        ajouterOption(question) {
            const nouvelleOption = { label: `Option ${question.options.filter(o => !o.is_open_ended).length + 1}`, is_open_ended: false };

            if (question.options.length > 0 && question.options[question.options.length - 1].is_open_ended) {
                question.options.splice(question.options.length - 1, 0, nouvelleOption);
            } else {
                question.options.push(nouvelleOption);
            }
        },
        ajouterOptionAutre(question) {
            // Vérifie s'il y a déjà une option "Autre"
            const existeDeja = question.options.some(o => o.is_open_ended);
            if (!existeDeja) {
                question.options.push({ label: 'Autre', is_open_ended: true });
            }
        },

        supprimerOption(question, index) {
            question.options.splice(index, 1);
        },

        aUneOptionAutre(question) {
            return question.options && question.options.some(o => o.is_open_ended);
        },
        sauvegarderFormulaire() {
            if (!this.titreFormulaire.trim()) {
                this.afficherModaleErreurTitre = true;
                return;
            }
            const donneesFormulaire = new FormData();
            if (this.idQuestionnaire) {
                donneesFormulaire.append('id', this.idQuestionnaire);
            }
            donneesFormulaire.append('titre', this.titreFormulaire);
            donneesFormulaire.append('description', this.descriptionFormulaire);
            donneesFormulaire.append('questions', JSON.stringify(this.questions));



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
        definirActif(index) {
            this.indexQuestionActive = index;
        },
        allerAuxParametres() {
            if (this.idQuestionnaire) {
                window.location.href = `index.php?c=tableauDeBord&a=parametres&id=${this.idQuestionnaire}`;
            } else {
                alert("Veuillez d'abord sauvegarder le questionnaire pour acceder aux parametres.");
            }
        },
        questionsPossiblesCommeParent(currentIndex) {
            // A question can only depend on a previous question of type single/multiple choice
            return this.questions
                .slice(0, currentIndex)
                .filter(q => ['Choix multiples', 'Cases à cocher', 'single_choice', 'multiple_choice'].includes(q.type));
        },
        optionsPourQuestionParent(questionId) {
            if (!questionId) return [];
            const parentQ = this.questions.find(q => q.id === questionId);
            if (!parentQ || !parentQ.options) return [];
            // Remove 'is_open_ended' options from being a condition if desired, 
            // but for now let's just return all defined options.
            return parentQ.options;
        }
    }
});

app.mount('#app');