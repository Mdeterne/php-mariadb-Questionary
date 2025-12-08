import { createApp } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js';
// Assure-toi que le fichier mock-data.js existe ou commente cette ligne si tu n'en as pas besoin
import { mockMesQuestionnaires } from './mock-data.js';

createApp({
    data() {
        return {
            questionnaires: [],
            termeRecherche: '',
            isLoading: true,
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

        async loadQuestionnaires() {
            this.isLoading = true;
            console.log("[Front-End] Chargement des questionnaires depuis le serveur...");

            try {
                // FIXED: Route controller changed from 'espacePerso' to 'tableauDeBord'
                const res = await fetch('?c=tableauDeBord&a=getMesQuestionnaires', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });

                // Check content type to avoid parsing HTML error pages as JSON
                const contentType = res.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    const data = await res.json();
                    this.questionnaires = Array.isArray(data) ? data : [];
                    console.log('[Front-End] Données chargées depuis l\'API, count=', this.questionnaires.length);
                } else {
                    console.warn('[Front-End] Réponse non-JSON reçue (probablement erreur PHP ou 404), utilisation du mock.');
                    throw new Error("Réponse non-JSON");
                }

            } catch (err) {
                console.warn('[Front-End] Échec du chargement depuis l\'API, utilisation du mock en secours.', err);
                // fallback : utiliser le mock si présent, sinon vide
                this.questionnaires = (typeof mockMesQuestionnaires !== 'undefined') ? mockMesQuestionnaires : [];
            } finally {
                this.isLoading = false;
            }
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
        this.loadQuestionnaires();
    }

}).mount('#app-dashboard');