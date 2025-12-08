<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditeur - Questionary</title>
    
    <link rel="stylesheet" href="style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <header class="topbar">
        <div class="topbar__left">
            <a href="" class="topbar__logo">
                <span class="appicon" aria-hidden="true"></span>
                <span class="apptitle">QUESTIONARY</span>
            </a>    
        </div>
        
        <div class="topbar__right" aria-label="Université de Limoges">
            <span class="uni-badge" aria-hidden="true">uℓ</span> 
            <span class="uni-text">Université de Limoges</span>
        </div>
    </header>

    <div class="editor-container">

        <aside class="editor-toolbox">
            <div class="tool-item" draggable="true" data-type="Réponse courte" data-icon="fa-pen">
                <i class="fa-solid fa-pen"></i> Réponse Courte
            </div>
            
            <div class="tool-item" draggable="true" data-type="Paragraphe" data-icon="fa-align-left">
                <i class="fa-solid fa-align-left"></i> Paragraphe
            </div>
            
            <div class="tool-item" draggable="true" data-type="Cases à cocher" data-icon="fa-square-check">
                <i class="fa-regular fa-square-check"></i> Cases à cocher
            </div>
            
            <div class="tool-item" draggable="true" data-type="Choix multiples" data-icon="fa-circle-dot">
                <i class="fa-regular fa-circle-dot"></i> Choix multiples
            </div>
            
            <div class="tool-item" draggable="true" data-type="Jauge" data-icon="fa-sliders">
                <i class="fa-solid fa-sliders"></i> Jauge
            </div>
        </aside>

        <main class="editor-workspace">
            
            <div class="form-group title-group">
                <input type="text" class="input-field title-input" placeholder="Titre formulaire">
            </div>

            <div class="form-group drop-zone" id="zone-depot">
                <span class="placeholder-text">Glisser élément ici</span>
            </div>

            <button class="btn-add-question">
                Ajouter une question
            </button>

        </main>

        <aside class="editor-actions">
            <button class="btn-action save">Sauvegarder le questionnaire</button>
            <a href ="?c=tableauDeBord">
                <button class="btn-action quit" onclick="window.location.href='index.html'">Quitter</button>
            </a>
            <button class="btn-action settings">Paramètres</button>
        </aside>

    </div>

    <script src="js/creation_questionnaire.js"></script>

</body>
</html>