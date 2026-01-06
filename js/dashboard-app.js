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
            showImportSuccess: false,
            showImportError: false,
            lienImport: '',          // Stocke le texte du lien entré par l'utilisateur
            questionnaireToDelete: null, // ID du questionnaire à supprimer

            // --- QR CODE MODAL ---
            showQrModal: false,
            qrLink: '',
            qrTitle: '',
            qrPin: ''
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
        // La méthode appelée quand on clique sur l'image
        toggleUserMenu() {
            this.showUserMenu = !this.showUserMenu;
        },

        creerNouveau() {
            // Appel du bon contrôleur pour la création
            fetch('?c=tableauDeBord&a=creerNouveau', { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(donnees => {
                    // Si le backend fonctionne, utiliser le vrai ID, sinon simuler
                    const nouveauId = (donnees && donnees.nouveau_id) ? donnees.nouveau_id : 99;
                    window.location.href = `?c=createur&a=index&id=${nouveauId}`;
                })
                .catch(erreur => {
                    console.error("Erreur lors de la création", erreur);
                    // Si y'a un problème, on simule un ID
                    const nouveauIdSimule = 99;
                    window.location.href = `?c=createur&a=index&id=${nouveauIdSimule}`;
                });
        },

        // Demande de suppression (popup)
        supprimer(id) {
            this.questionnaireToDelete = id;
        },

        annulerSuppression() {
            this.questionnaireToDelete = null;
        },

        async confirmerSuppression() {
            const id = this.questionnaireToDelete;
            if (!id) return;

            try {
                const res = await fetch(`?c=tableauDeBord&a=supprimer&id=${id}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                // Mise à jour optimiste de l'interface
                this.questionnaires = this.questionnaires.filter(q => q.id !== id);
            } catch (erreur) {
                console.error("Erreur lors de la suppression", erreur);
            } finally {
                this.questionnaireToDelete = null;
            }
        },

        // Importation d'un questionnaire
        validerImport() {
            if (this.lienImport.trim() === '') {
                alert("Veuillez entrer un code PIN valide.");
                return;
            }
            window.location.href = '?c=tableauDeBord&a=importer&pin=' + this.lienImport;
        },

        // QR Code, url du questionnaire et lien
        afficherQrCode(pin, titre) {

            const urlBase = window.location.origin + window.location.pathname;

            this.qrLink = `${urlBase}?c=home&a=valider&pin=${pin}`;
            this.qrTitle = titre;
            this.qrPin = pin;
            this.showQrModal = true;
        },

        async downloadQrImage() {
            const canvas = document.querySelector('.modal-card canvas');
            if (canvas) {
                const lien = document.createElement('a');
                lien.download = 'qrcode.png';
                lien.href = canvas.toDataURL('image/png');
                document.body.appendChild(lien);
                lien.click();
                document.body.removeChild(lien);
            }
        },

        closeQrModal() {
            this.showQrModal = false;
            this.qrLink = '';
            this.qrPin = '';
        }
    },
    mounted() {

        const paramsUrl = new URLSearchParams(window.location.search);
        const statutImport = paramsUrl.get('import');

        if (statutImport === 'success') {
            this.showImportSuccess = true;
            // Nettoyage de l'URL
            const nouvelleUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?c=tableauDeBord';
            window.history.replaceState({ path: nouvelleUrl }, '', nouvelleUrl);
        } else if (statutImport === 'error') {
            this.showImportError = true;
            // Nettoyage de l'URL
            const nouvelleUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?c=tableauDeBord';
            window.history.replaceState({ path: nouvelleUrl }, '', nouvelleUrl);
        }
    }

}).mount('#app-dashboard');