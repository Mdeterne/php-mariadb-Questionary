import { createApp } from 'vue';
import QrcodeVue from 'qrcode.vue';

const TAG_COLORS = {
    'BUT1': { bg: '#fff3e0', color: '#e65100', border: '#ffb74d' },
    'BUT2': { bg: '#e8f5e9', color: '#2e7d32', border: '#81c784' },
    'BUT3': { bg: '#ede7f6', color: '#4527a0', border: '#9575cd' },
};

const ANNEE_STYLE = { bg: '#e8eaf6', color: '#3949ab', border: '#7986cb' };

createApp({
    components: {
        QrcodeVue
    },
    data() {
        return {
            questionnaires: (typeof window.serverQuestionnaires !== 'undefined') ? window.serverQuestionnaires : [],
            notifications: (typeof window.serverNotifications !== 'undefined') ? window.serverNotifications : [],
            termeRecherche: '',
            isLoading: false,
            showUserMenu: false,

            showImportModal: false,
            showImportSuccess: false,
            showImportError: false,
            showTemplateModal: false,
            lienImport: '',
            questionnaireToDelete: null,

            // Filtres déroulants
            filtreAnnee: '',
            filtreBut: '',

            // Gestion des tags
            questionnairePourTags: null,

            // Gestion du QR Code
            showQrModal: false,
            qrLink: '',
            qrTitle: '',
            qrPin: '',
            qrId: null
        };
    },

    computed: {
        anneesDisponibles() {
            const annees = new Set();
            this.questionnaires.forEach(q => {
                (q.tags || []).forEach(t => { if (/^\d{4}$/.test(t)) annees.add(t); });
            });
            return Array.from(annees).sort().reverse();
        },

        questionnairesFiltres() {
            let liste = this.questionnaires;

            // Filtre texte
            if (this.termeRecherche !== '') {
                const recherche = this.termeRecherche.toLowerCase();
                liste = liste.filter(q => q.titre.toLowerCase().includes(recherche));
            }

            // Filtre année
            if (this.filtreAnnee) {
                liste = liste.filter(q => (q.tags || []).includes(this.filtreAnnee));
            }

            // Filtre BUT
            if (this.filtreBut) {
                liste = liste.filter(q => (q.tags || []).includes(this.filtreBut));
            }

            return liste;
        },

        unreadNotificationsCount() {
            return this.notifications.filter(n => !n.read).length;
        }
    },

    methods: {

        tagStyle(tag, actif) {
            const c = TAG_COLORS[tag] || ANNEE_STYLE;
            if (actif) {
                return `background:${c.color}; color:white; border:1.5px solid ${c.color}; font-size:0.78rem; font-weight:700; padding:5px 14px; border-radius:20px; cursor:pointer; transition:all 0.15s;`;
            }
            return `background:${c.bg}; color:${c.color}; border:1.5px solid ${c.border}; font-size:0.78rem; font-weight:700; padding:5px 14px; border-radius:20px; cursor:pointer; transition:all 0.15s;`;
        },

        tagBadgeStyle(tag) {
            // Tous les tags (Année ou BUT) partagent désormais le même style DA :
            // Fond blanc, bordure var(--ink), texte var(--ink) (noir)
            return `background:white; color:var(--ink); border:1px solid var(--ink);`;
        },

        // --- Gestion des tags d'un questionnaire ---
        ouvrirMenuTag(q) {
            this.questionnairePourTags = JSON.parse(JSON.stringify(q)); // copie réactive
        },

        fermerMenuTag() {
            this.questionnairePourTags = null;
        },

        async ajouterTag(surveyId, tag) {
            const res = await fetch('?c=tableauDeBord&a=addTag', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ survey_id: surveyId, tag })
            });
            const data = await res.json();
            if (data.status === 'success') {
                this._syncTags(surveyId, data.tags);
            }
        },

        async supprimerTag(surveyId, tag) {
            const res = await fetch('?c=tableauDeBord&a=removeTag', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ survey_id: surveyId, tag })
            });
            const data = await res.json();
            if (data.status === 'success') {
                this._syncTags(surveyId, data.tags);
            }
        },

        _syncTags(surveyId, tags) {
            const q = this.questionnaires.find(q => q.id === surveyId);
            if (q) q.tags = tags;
            if (this.questionnairePourTags && this.questionnairePourTags.id === surveyId) {
                this.questionnairePourTags.tags = tags;
            }
        },

        // --- User menu ---
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
                    window.location.href = `?c=createur&a=index&id=99`;
                });
        },

        // --- Suppression ---
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
                await fetch(`?c=tableauDeBord&a=supprimer&id=${id}`, {
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

        // --- Import ---
        validerImport() {
            if (this.lienImport.trim() === '') {
                alert("Veuillez entrer un code PIN valide.");
                return;
            }
            window.location.href = '?c=tableauDeBord&a=importer&pin=' + this.lienImport;
        },

        // --- QR Code ---
        afficherQrCode(pin, titre, id) {
            const urlBase = window.location.origin + window.location.pathname;
            this.qrLink = `${urlBase}?c=home&a=valider&pin=${pin}`;
            this.qrTitle = titre;
            this.qrPin = pin;
            this.qrId = id;
            this.showQrModal = true;
        },

        downloadPdf() {
            if (this.qrId) {
                window.open('export_pdf.php?id=' + this.qrId, '_blank');
            }
        },

        async downloadQrImage() {
            const originalCanvas = document.querySelector('.modal-card canvas');
            if (originalCanvas) {
                const nouveauCanvas = document.createElement('canvas');
                const ctx = nouveauCanvas.getContext('2d');
                const padding = 20;
                const texteHauteur = 40;
                nouveauCanvas.width = originalCanvas.width + (padding * 2);
                nouveauCanvas.height = originalCanvas.height + (padding * 2) + texteHauteur;
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, nouveauCanvas.width, nouveauCanvas.height);
                ctx.drawImage(originalCanvas, padding, padding);
                ctx.font = 'bold 24px Arial';
                ctx.fillStyle = '#000000';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
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
        },

        selectTemplate(templateId) {
            window.location.href = `?c=createur&a=nouveauFormulaire&template=${templateId}`;
        }
    },

    mounted() {
        const paramsUrl = new URLSearchParams(window.location.search);
        const statutImport = paramsUrl.get('import');

        if (statutImport === 'success') {
            this.showImportSuccess = true;
            const nouvelleUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?c=tableauDeBord';
            window.history.replaceState({ path: nouvelleUrl }, '', nouvelleUrl);
        } else if (statutImport === 'error') {
            this.showImportError = true;
            const nouvelleUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?c=tableauDeBord';
            window.history.replaceState({ path: nouvelleUrl }, '', nouvelleUrl);
        }
    }

}).mount('#app-dashboard');