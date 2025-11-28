document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. SÉLECTION DES ÉLÉMENTS ---
    const draggables = document.querySelectorAll('.tool-item');
    const dropZone = document.getElementById('zone-depot');
    const placeholder = document.querySelector('.placeholder-text');

    // --- 2. GESTION DU DÉBUT DU GLISSER (Côté Outils) ---
    draggables.forEach(draggable => {
        draggable.addEventListener('dragstart', (e) => {
            draggable.classList.add('dragging');
            // On stocke le type de question et l'icône dans le transfert de données
            // On utilise .dataset pour récupérer les attributs data-type et data-icon
            e.dataTransfer.setData('type', draggable.dataset.type);
            e.dataTransfer.setData('icon', draggable.dataset.icon);
        });

        draggable.addEventListener('dragend', () => {
            draggable.classList.remove('dragging');
        });
    });

    // --- 3. GESTION DE LA ZONE DE DÉPÔT (Drop Zone) ---
    
    // Quand un élément survole la zone
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault(); // OBLIGATOIRE pour autoriser le drop
        dropZone.classList.add('hover'); // Ajoute l'effet visuel (pointillés)
    });

    // Quand l'élément quitte la zone sans être lâché
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('hover');
    });

    // Quand on lâche l'élément (DROP)
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('hover');

        // Masquer le texte "Glisser un élément ici"
        if (placeholder) placeholder.style.display = 'none';

        // Récupérer les données
        const type = e.dataTransfer.getData('type');
        const iconClass = e.dataTransfer.getData('icon');

        // Si on a bien récupéré un type, on crée la question
        if (type) {
            creerNouvelleQuestion(type, iconClass);
        }
    });


    // --- 4. FONCTION POUR CRÉER LE HTML DE LA QUESTION ---
    function creerNouvelleQuestion(titre, iconClass) {
        // Création de la div conteneur
        const div = document.createElement('div');
        div.classList.add('dropped-item');

        // Contenu HTML de la question
        div.innerHTML = `
            <div class="item-header">
                <span class="item-title"><i class="fa-solid ${iconClass}"></i> ${titre}</span>
                <button class="btn-delete" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
            </div>
            <div class="item-body">
                <input type="text" class="input-question" placeholder="Posez votre question ici...">
                </div>
        `;

        // Ajout de l'événement de suppression sur le bouton poubelle
        const btnDelete = div.querySelector('.btn-delete');
        btnDelete.addEventListener('click', () => {
            div.remove();
            
            // Si c'était le dernier élément, on réaffiche le placeholder
            // (On vérifie s'il reste des éléments 'dropped-item' dans la zone)
            const restants = dropZone.querySelectorAll('.dropped-item');
            if (restants.length === 0 && placeholder) {
                placeholder.style.display = 'block';
            }
        });

        // Ajout de l'élément à la zone de dépôt
        dropZone.appendChild(div);

        
    }

});