import { createApp } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js';

createApp({
    data() {
        return {
            // Initialisation avec les données injectées par PHP (window.serverQuestionnaires)
            questionnaires: (typeof window.serverQuestionnaires !== 'undefined') ? window.serverQuestionnaires : [],
            termeRecherche: '',
            isLoading: false,
            showUserMenu: false, // IMPORTANT : Par défaut le menu est caché

            // --- NOUVEAUX CHAMPS POUR L'IMPORTATION ---
            showImportModal: false, // Contrôle l'affichage de la modale floutée
            lienImport: ''          // Stocke le texte du lien entré par l'utilisateur
        };
    },

    computed: {
        questionnairesFiltres() {
            if (this.termeRecherche === '') {
                return this.questionnaires;
            }

            const recherche = this.termeRecherche.toLowerCase();
            return this.questionnaires.filter(q =>
                q.titre.toLowerCase().includes(recherche)
            );
        }
    },

    methods: {
        // IMPORTANT : La méthode appelée quand on clique sur l'image
        toggleUserMenu() {
            this.showUserMenu = !this.showUserMenu;
        },

        creerNouveau() {
            console.log("[Front-End] Simulation: Demande de création...");
            // FIXED: Call the correct controller for creation
            fetch('?c=tableauDeBord&a=creerNouveau', { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    // If backend works, use the real ID, otherwise simulate
                    const nouveauId = (data && data.nouveau_id) ? data.nouveau_id : 99;
                    console.log(`[Front-End] Redirection vers créateur avec ID ${nouveauId}`);
                    window.location.href = `?c=createur&a=index&id=${nouveauId}`;
                })
                .catch(err => {
                    console.error("Erreur création", err);
                    // Fallback simulation
                    const nouveauIdSimule = 99;
                    window.location.href = `?c=createur&a=index&id=${nouveauIdSimule}`;
                });
        },

        async supprimer(id) {
            if (!confirm("Êtes-vous sûr de vouloir supprimer ce questionnaire ?")) {
                return;
            }
            console.log(`[Front-End] Demande de suppression id ${id}...`);

            try {
                const res = await fetch(`?c=tableauDeBord&a=supprimer&id=${id}`, {
                    method: 'GET', // Should be POST ideally, but keeping GET for simple routing compat
                    headers: { 'Accept': 'application/json' }
                });
                // Optimistic UI update
                this.questionnaires = this.questionnaires.filter(q => q.id !== id);
                console.log(`[Front-End] Questionnaire ${id} supprimé de l'affichage.`);
            } catch (e) {
                console.error("Erreur suppression", e);
            }
        },

        // --- NOUVELLE MÉTHODE D'IMPORTATION ---
        validerImport() {
            // Petite vérification simple
            if (this.lienImport.trim() === '') {
                alert("Veuillez entrer un lien valide.");
                return;
            }

            console.log("Importation du lien : ", this.lienImport);

            // Simulation d'une action réussie
            alert("Questionnaire importé avec succès (Simulation) !");

            // On remet à zéro et on ferme la modale
            this.lienImport = '';
            this.showImportModal = false;
        }
    },
    mounted() {
        // Plus besoin de charger via AJAX au démarrage car les données sont injectées par PHP
        console.log("Application montée. Données initiales :", this.questionnaires);
    }

}).mount('#app-dashboard');