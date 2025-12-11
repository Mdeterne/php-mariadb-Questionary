document.addEventListener('DOMContentLoaded', () => {

    // --- 1. Gestion du bouton Copier ---
    const btnCopy = document.getElementById('btn-copy');
    const inputLink = document.getElementById('share-link');

    btnCopy.addEventListener('click', () => {
        // Sélectionne le texte
        inputLink.select();
        inputLink.setSelectionRange(0, 99999); // Pour mobile

        // Copie dans le presse-papier
        navigator.clipboard.writeText(inputLink.value).then(() => {
            // Feedback visuel temporaire
            const originalText = btnCopy.innerText;
            btnCopy.innerText = "Copié !";
            btnCopy.style.backgroundColor = "#d4edda"; // Vert clair

            setTimeout(() => {
                btnCopy.innerText = originalText;
                btnCopy.style.backgroundColor = "";
            }, 2000);
        }).catch(err => {
            console.error('Erreur lors de la copie :', err);
        });
    });

    // --- 2. Gestion du bouton Supprimer (MODALE) ---
    const btnDelete = document.getElementById('btn-delete');
    const modalDelete = document.getElementById('modal-delete');
    const btnCancelDelete = document.getElementById('btn-cancel-delete');
    const btnConfirmDelete = document.getElementById('btn-confirm-delete');

    // Ouvrir la modale
    btnDelete.addEventListener('click', () => {
        modalDelete.style.display = 'flex';
    });

    // Fermer la modale (Annuler)
    btnCancelDelete.addEventListener('click', () => {
        modalDelete.style.display = 'none';
    });

    // Fermer si clic en dehors
    modalDelete.addEventListener('click', (e) => {
        if (e.target === modalDelete) {
            modalDelete.style.display = 'none';
        }
    });

    // Confirmer suppression
    btnConfirmDelete.addEventListener('click', () => {
        // Simulation suppression
        console.log("Suppression confirmée via modale.");
        modalDelete.style.display = 'none';

        // Redirection simulée
        // window.location.href = "/accueil";
        // Pour la démo, on simule juste une alerte "Supprimé" ou redirection
        alert("Questionnaire supprimé (Simulation).");
    });

    // --- 3. Gestion du bouton Enregistrer ---
    const btnSave = document.getElementById('btn-save');

    btnSave.addEventListener('click', () => {
        // Simulation de récupération des données
        const formData = {
            link: inputLink.value,
            acceptResponses: document.getElementById('toggle-access').checked,
            dateStart: document.getElementById('date-start').value,
            dateEnd: document.getElementById('date-end').value,
            notifResponse: document.getElementById('notif-response').checked,
            notifLimit: document.getElementById('notif-limit').checked,
            notifInvalid: document.getElementById('notif-invalid').checked
        };

        console.log("Données à envoyer au serveur :", formData);

        // Petit effet de chargement simulé
        btnSave.innerText = "Enregistrement...";
        setTimeout(() => {
            alert("Modifications enregistrées avec succès !");
            btnSave.innerText = "Enregistrer les modifications";
        }, 800);
    });

});