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
            console.log("Clic menu ! État actuel : " + this.showUserMenu);
            this.showUserMenu = !this.showUserMenu;
        },

        async loadQuestionnaires() {
            this.isLoading = true;
            console.log("[Front-End] Chargement des questionnaires depuis le serveur...");

            try {
                const res = await fetch('?c=espacePerso&a=getMesQuestionnaires', { method: 'GET', headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Réponse non OK ' + res.status);
                const data = await res.json();
                // Si l'API renvoie null/undefined, on force un tableau vide
                this.questionnaires = Array.isArray(data) ? data : [];
                console.log('[Front-End] Données chargées depuis l\'API, count=', this.questionnaires.length);
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
            setTimeout(() => {
                const nouveauIdSimule = 99;
                console.log(`[Front-End] Simulation: Questionnaire ${nouveauIdSimule} créé.`);
                window.location.href = `?c=createur&a=index&id=${nouveauIdSimule}`;
            }, 300);
        },
        
        supprimer(id) {
            if (!confirm("Êtes-vous sûr de vouloir supprimer ce questionnaire ?")) {
                return;
            }
            console.log(`[Front-End] Simulation: Demande de suppression id ${id}...`);
            setTimeout(() => {
                this.questionnaires = this.questionnaires.filter(q => q.id !== id);
                console.log(`[Front-End] Simulation: Questionnaire ${id} supprimé.`);
            }, 300);
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