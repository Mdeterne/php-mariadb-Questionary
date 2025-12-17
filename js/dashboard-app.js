import { createApp } from 'vue';
import QrcodeVue from 'qrcode.vue';

createApp({
    components: {
        QrcodeVue
    },
    data() {
        return {
            // Initialisation avec les données injectées par PHP (window.serverQuestionnaires)
            questionnaires: (typeof window.serverQuestionnaires !== 'undefined') ? window.serverQuestionnaires : [],
            termeRecherche: '',
            isLoading: false,
            showUserMenu: false, // IMPORTANT : Par défaut le menu est caché

            // --- NOUVEAUX CHAMPS POUR L'IMPORTATION ---
            showImportModal: false, // Contrôle l'affichage de la modale floutée
            lienImport: '',          // Stocke le texte du lien entré par l'utilisateur
            questionnaireToDelete: null, // ID du questionnaire à supprimer

            // --- QR CODE MODAL ---
            showQrModal: false,
            qrLink: '',
            qrTitle: ''
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

        // Demande de suppression (ouvre la modale)
        supprimer(id) {
            this.questionnaireToDelete = id;
        },

        annulerSuppression() {
            this.questionnaireToDelete = null;
        },

        async confirmerSuppression() {
            const id = this.questionnaireToDelete;
            if (!id) return;

            console.log(`[Front-End] Validation suppression id ${id}...`);

            try {
                const res = await fetch(`?c=tableauDeBord&a=supprimer&id=${id}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                // Optimistic UI update
                this.questionnaires = this.questionnaires.filter(q => q.id !== id);
                console.log(`[Front-End] Questionnaire ${id} supprimé.`);
            } catch (e) {
                console.error("Erreur suppression", e);
            } finally {
                this.questionnaireToDelete = null;
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
        },

        // --- QR CODE METHODS ---
        afficherQrCode(pin, titre) {
            // Construit l'URL absolue pour répondre au questionnaire
            // On suppose que l'URL actuelle est .../index.php ou .../
            // On redirige vers ?c=home&a=valider&pin=...
            const baseUrl = window.location.origin + window.location.pathname;
            // On s'assure de ne pas doubler le slash si pathname finit par /
            // Mais pathname finit généralement par .php ou dossier.
            // Une façon safe de reconstruire l'url :

            // Si on est sur /index.php, on garde /index.php
            // Si on est sur /, on garde /

            // On retire les parametres GET actuels
            this.qrLink = `${baseUrl}?c=home&a=valider&pin=${pin}`;
            this.qrTitle = titre;
            this.showQrModal = true;
        },

        async downloadQrImage() {
            const canvas = document.querySelector('.modal-card canvas');
            if (canvas) {
                const link = document.createElement('a');
                link.download = 'qrcode.png';
                link.href = canvas.toDataURL('image/png');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        },

        closeQrModal() {
            this.showQrModal = false;
            this.qrLink = '';
        }
    },
    mounted() {
        // Plus         besoin de charger via AJAX au démarrage car les données sont injectées par PHP
        console.log("Application montée. Données initiales :", this.questionnaires);
    }

}).mount('#app-dashboard');