document.addEventListener('DOMContentLoaded', () => {

    // Gestion du bouton de copie
    const btnCopy = document.getElementById('btn-copy');
    const inputLink = document.getElementById('share-link');

    btnCopy.addEventListener('click', () => {
        const textToCopy = inputLink.value;

        // Affichage du retour visuel après copie
        const showSuccessFeedback = () => {
            const originalText = "Copier";
            btnCopy.innerText = "Copié !";
            btnCopy.style.backgroundColor = "#d4edda";

            setTimeout(() => {
                btnCopy.innerText = originalText;
                btnCopy.style.backgroundColor = "";
            }, 2000);
        };

        // Méthode principale : Utilisation de l'API Clipboard
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(textToCopy).then(() => {
                showSuccessFeedback();
            }).catch(err => {
                console.warn('Echec API Clipboard, tentative fallback...', err);
                fallbackCopyTextToClipboard(textToCopy);
            });
        } else {
            // Méthode de secours : Utilisation de execCommand
            fallbackCopyTextToClipboard(textToCopy);
        }

        function fallbackCopyTextToClipboard(text) {
            try {
                const textArea = document.createElement("textarea");
                textArea.value = text;

                // Création d'un élément textarea invisible pour la sélection
                textArea.style.position = "fixed";
                textArea.style.left = "-9999px";
                textArea.style.top = "0";
                document.body.appendChild(textArea);

                textArea.focus();
                textArea.select();

                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);

                if (successful) {
                    showSuccessFeedback();
                } else {
                    console.error('Fallback: Echec de la commande copy');
                    alert("Impossible de copier automatiquement. Veuillez copier le code manuellement.");
                }
            } catch (err) {
                console.error('Fallback: Erreur inattendue', err);
                alert("Impossible de copier le code.");
            }
        }
    });

    // Gestion de la modale de suppression
    const btnDelete = document.getElementById('btn-delete');
    const modalDelete = document.getElementById('modal-delete');
    const btnCancelDelete = document.getElementById('btn-cancel-delete');
    const btnConfirmDelete = document.getElementById('btn-confirm-delete');

    // Ouverture de la modale
    btnDelete.addEventListener('click', () => {
        modalDelete.style.display = 'flex';
    });

    // Fermeture de la modale
    btnCancelDelete.addEventListener('click', () => {
        modalDelete.style.display = 'none';
    });

    // Fermeture au clic sur l'arrière-plan
    modalDelete.addEventListener('click', (e) => {
        if (e.target === modalDelete) {
            modalDelete.style.display = 'none';
        }
    });

    // Traitement de la suppression du questionnaire
    btnConfirmDelete.addEventListener('click', () => {
        const surveyId = document.querySelector('.settings-main').dataset.surveyId;

        fetch(`?c=tableauDeBord&a=supprimer&id=${surveyId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
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

    // Gestion du bouton de sauvegarde
    const btnSave = document.getElementById('btn-save');
    const toggleAccess = document.getElementById('toggle-access');
    const modalSuccess = document.getElementById('modal-success');
    const btnConfirmSuccess = document.getElementById('btn-confirm-success');

    // Redirection après validation de la modale de succès
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
        e.preventDefault();
        saveSettings(true);
    });


    // Gestion du bouton d'annulation
    const btnCancel = document.getElementById('btn-cancel');
    if (btnCancel) {
        btnCancel.addEventListener('click', () => {
            const surveyId = document.querySelector('.settings-main').dataset.surveyId;
            window.location.href = '?c=createur&a=editer&id=' + surveyId;
        });
    }

});