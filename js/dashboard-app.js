import { createApp } from 'vue';
import QrcodeVue from 'qrcode.vue';

createApp({
    components: {
        QrcodeVue
    },
    data() {
        return {
            // Initialisation des données réactives 
            questionnaires: (typeof window.serverQuestionnaires !== 'undefined') ? window.serverQuestionnaires : [],
            notifications: (typeof window.serverNotifications !== 'undefined') ? window.serverNotifications : [],
            termeRecherche: '',
            isLoading: false,
            showUserMenu: false,

            showImportModal: false,
            showImportSuccess: false,
            showImportError: false,
            lienImport: '',
            questionnaireToDelete: null,

            // Gestion du QR Code 
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
        },
        unreadNotificationsCount() {
            return this.notifications.filter(n => !n.read).length;
        }
    },

    methods: {
        toggleUserMenu() {
            this.showUserMenu = !this.showUserMenu;
        },

        creerNouveau() {
            fetch('?c=tableauDeBord&a=creerNouveau', { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(donnees => {
                    const nouveauId = (donnees && donnees.nouveau_id) ? donnees.nouveau_id : 99;
                    window.location.href = `?c=createur&a=index&id=${nouveauId}`;
                })
                .catch(erreur => {
                    console.error("Erreur lors de la création", erreur);
                    const nouveauIdSimule = 99;
                    window.location.href = `?c=createur&a=index&id=${nouveauIdSimule}`;
                });
        },

        // Gestion de la modale de suppression
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
                this.questionnaires = this.questionnaires.filter(q => q.id !== id);
            } catch (erreur) {
                console.error("Erreur lors de la suppression", erreur);
            } finally {
                this.questionnaireToDelete = null;
            }
        },

        // Logique d'importation d'un questionnaire via PIN
        validerImport() {
            if (this.lienImport.trim() === '') {
                alert("Veuillez entrer un code PIN valide.");
                return;
            }
            window.location.href = '?c=tableauDeBord&a=importer&pin=' + this.lienImport;
        },

        // Affichage du QR Code et génération du lien d'accès
        afficherQrCode(pin, titre) {

            const urlBase = window.location.origin + window.location.pathname;

            this.qrLink = `${urlBase}?c=home&a=valider&pin=${pin}`;
            this.qrTitle = titre;
            this.qrPin = pin;
            this.showQrModal = true;
        },

        async downloadQrImage() {
            const originalCanvas = document.querySelector('.modal-card canvas');
            if (originalCanvas) {
                // Création d'un nouveau canvas temporaire incluant le texte
                const nouveauCanvas = document.createElement('canvas');
                const ctx = nouveauCanvas.getContext('2d');

                const padding = 20;
                const texteHauteur = 40;

                nouveauCanvas.width = originalCanvas.width + (padding * 2);
                nouveauCanvas.height = originalCanvas.height + (padding * 2) + texteHauteur;

                // Application d'un fond blanc
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, nouveauCanvas.width, nouveauCanvas.height);

                // Dessin du QR Code source
                ctx.drawImage(originalCanvas, padding, padding);

                // Configuration du style pour le texte du PIN
                ctx.font = 'bold 24px Arial';
                ctx.fillStyle = '#000000';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';

                // Ajout du code PIN en bas de l'image
                const textX = nouveauCanvas.width / 2;
                const textY = originalCanvas.height + padding + (texteHauteur / 2);
                ctx.fillText(`Code: ${this.qrPin}`, textX, textY);

                const lien = document.createElement('a');
                lien.download = 'qrcode_questionnaire_' + this.qrPin + '.png';
                lien.href = nouveauCanvas.toDataURL('image/png');
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
            // Nettoyage de l'URL pour retirer le paramètre 'import' après traitement
            const nouvelleUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?c=tableauDeBord';
            window.history.replaceState({ path: nouvelleUrl }, '', nouvelleUrl);
        } else if (statutImport === 'error') {
            this.showImportError = true;
            // Nettoyage de l'URL pour retirer le paramètre 'import' après traitement
            const nouvelleUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?c=tableauDeBord';
            window.history.replaceState({ path: nouvelleUrl }, '', nouvelleUrl);
        }
    }

}).mount('#app-dashboard');