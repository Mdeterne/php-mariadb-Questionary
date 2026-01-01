document.addEventListener('DOMContentLoaded', () => {

    // Bouton copier
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

    // Bouton supprimer
    const btnDelete = document.getElementById('btn-delete');
    const modalDelete = document.getElementById('modal-delete');
    const btnCancelDelete = document.getElementById('btn-cancel-delete');
    const btnConfirmDelete = document.getElementById('btn-confirm-delete');

    // Ouvrir la popup supprimer
    btnDelete.addEventListener('click', () => {
        modalDelete.style.display = 'flex';
    });

    // Fermer la popup supprimer
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
        const surveyId = document.querySelector('.settings-main').dataset.surveyId;

        fetch(`?c=tableauDeBord&a=supprimer&id=${surveyId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Redirection vers le tableau de bord après suppression
                    window.location.href = '?c=tableauDeBord';
                } else {
                    alert("Erreur lors de la suppression : " + (data.message || "Erreur inconnue"));
                    modalDelete.style.display = 'none';
                }
            })
            .catch(err => {
                console.error('Erreur:', err);
                alert("Erreur de communication avec le serveur.");
                modalDelete.style.display = 'none';
            });
    });

    // Bouton enregistrer
    const btnSave = document.getElementById('btn-save');
    const toggleAccess = document.getElementById('toggle-access');
    const modalSuccess = document.getElementById('modal-success');
    const btnConfirmSuccess = document.getElementById('btn-confirm-success');

    // Redirection après clic sur OK
    btnConfirmSuccess.addEventListener('click', () => {
        const surveyId = document.querySelector('.settings-main').dataset.surveyId;
        window.location.href = '?c=createur&a=editer&id=' + surveyId;
    });

    const saveSettings = (showSuccessAlert = true) => {
        const surveyId = document.querySelector('.settings-main').dataset.surveyId;

        const formData = {
            id: surveyId,
            acceptResponses: toggleAccess.checked,
            dateStart: document.getElementById('date-start').value,
            dateEnd: document.getElementById('date-end').value,
            notifResponse: document.getElementById('notif-response').checked,
            notifLimit: document.getElementById('notif-limit') ? document.getElementById('notif-limit').checked : false,
            notifInvalid: document.getElementById('notif-invalid') ? document.getElementById('notif-invalid').checked : false
        };

        const originalBtnText = btnSave.innerText;
        btnSave.innerText = "Enregistrement...";
        btnSave.disabled = true;

        fetch('?c=tableauDeBord&a=saveSettings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    if (showSuccessAlert) {
                        // Afficher la modale de succès
                        modalSuccess.style.display = 'flex';
                    }
                } else {
                    alert("Erreur : " + (data.message || "Une erreur est survenue"));
                }
            })
            .catch(err => {
                console.error('Erreur:', err);
                alert("Erreur de communication avec le serveur.");
            })
            .finally(() => {
                btnSave.innerText = "Enregistrer les modifications";
                btnSave.disabled = false;
            });
    };

    btnSave.addEventListener('click', (e) => {
        e.preventDefault(); // Prevenir en cas de clic sur un lien
        saveSettings(true);
    });


    // Bouton Annuler
    const btnCancel = document.getElementById('btn-cancel');
    if (btnCancel) {
        btnCancel.addEventListener('click', () => {
            const surveyId = document.querySelector('.settings-main').dataset.surveyId;
            window.location.href = '?c=createur&a=editer&id=' + surveyId;
        });
    }

});