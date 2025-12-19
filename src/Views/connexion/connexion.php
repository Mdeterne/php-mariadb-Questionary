<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Questionary</title>

  <!-- Police moderne et sobre -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/variables.css" />
  <link rel="stylesheet" href="css/components/topbar.css" />
  <link rel="stylesheet" href="css/components/buttons.css" />
  <link rel="stylesheet" href="css/components/inputs.css" />
  <link rel="stylesheet" href="css/components/cards.css" />
  <link rel="stylesheet" href="css/components/footer.css" />
</head>

<body>
  <!-- Topbar -->
  <?php require_once __DIR__ . '/../components/header.php'; ?>

  <!-- Contenu principal -->
  <main class="center">
    <section class="card" role="region" aria-labelledby="cardTitle">

      <form method="POST" action="?c=connexion&a=connexion">

        <div class="input-wrap">
          <label for="username" class="input-label">Nom d'utilisateur :</label>
          <input id="username" name="nom_utilisateur" type="text" inputmode="text" placeholder=""
            autocomplete="username" value="<?php echo $username_value ?? ''; ?>" />
          <button class="clear" type="button" aria-label="Effacer le nom d'utilisateur">✕</button>
        </div>

        <div class="input-wrap">
          <label for="password" class="input-label">Mot de passe :</label>
          <input id="password" name="mot_de_passe" type="password" inputmode="text" placeholder=""
            autocomplete="current-password" value="<?php echo $username_value ?? ''; ?>" /> <button class="clear"
            type="button" aria-label="Effacer le mot de passe">✕</button>
        </div>


        <button class="btn btn-primary" type="submit">Se connecter</button>
      </form>

      <a href="?c=creerUnCompte&a=index" class="btn btn-secondary">Créer un compte</a>

      <!-- Pied de page -->
      <footer class="footer">
        <nav class="footer__links" aria-label="Liens légaux">
          <a href="?c=home&a=conditionGenerales" title="Conditions générales">Conditions générales</a>
          <span aria-hidden="true">|</span>
          <a href="?c=home&a=confidentialite" title="Confidentialité">Confidentialité</a>
          <span aria-hidden="true">|</span>
          <a href="?c=home&a=utilisationCookie" title="Utilisation des cookies">Utilisation des cookies</a>
        </nav>
      </footer>

      <script>
        document.addEventListener("DOMContentLoaded", function () {

          const clearButtons = document.querySelectorAll('.clear');

          clearButtons.forEach(button => {
            button.addEventListener('click', function () {

              const inputWrapper = this.closest('.input-wrap');

              if (inputWrapper) {
                const inputField = inputWrapper.querySelector('input');

                if (inputField) {
                  inputField.value = '';
                  inputField.focus();
                }
              }
            });
          });
        });
      </script>

</body>

</html>