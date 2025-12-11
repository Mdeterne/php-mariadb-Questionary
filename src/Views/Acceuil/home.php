<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Questionary</title>

  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/variables.css" />
  <link rel="stylesheet" href="css/components/topbar.css" />
  <link rel="stylesheet" href="css/components/buttons.css" />
  <link rel="stylesheet" href="css/components/inputs.css" />
  <link rel="stylesheet" href="css/components/cards.css" />
  <link rel="stylesheet" href="css/components/footer.css" />
  <link rel="stylesheet" href="css/pages/home.css" />

</head>

<body>
  <?php
  $showCreateLink = true;
  require_once __DIR__ . '/../components/header.php';
  ?>

  <main class="center">
    <div class="content-grid">
      <section class="card" role="region" aria-label="Accéder à un questionnaire">
        <form method="POST" action="?c=home&a=valider">
          <div class="input-wrap">
            <label for="pin" class="input-label"></label>

            <input id="pin" name="pin" type="text" inputmode="numeric" placeholder="Code pin"
              autocomplete="one-time-code" value="<?php echo $pin_value ?? ''; ?>" />
            <button class="clear" type="button" aria-label="Effacer le code">✕</button>
          </div>
          <button class="btn btn-primary" type="submit">Valider</button>
        </form>
      </section>

      <section class="description" aria-labelledby="descriptionTitle">
        <h1 id="descriptionTitle">La puissance pour créer. La simplicité pour répondre.</h1>
        <p>
          QUESTIONARY est conçue pour optimiser l'ensemble du processus.
          Un constructeur de formulaires avancé permet de bâtir des enquêtes
          complexes rapidement, tandis que l'interface de réponse,
          épurée et réactive, assure une expérience utilisateur intuitive
          sur tous les appareils pour maximiser le taux de participation.
        </p>
      </section>
      <footer class="footer">
        <nav class="footer__links" aria-label="Liens légaux">
          <a href="?c=tableauDeBord&a=conditionGenerales" title="Conditions générales">Conditions générales</a>
          <span aria-hidden="true">|</span>
          <a href="?c=tableauDeBord&a=confidentialite" title="Confidentialité">Confidentialité</a>
          <span aria-hidden="true">|</span>
          <a href="index.php?c=tableauDeBord&a=utilisationCookie" title="Utilisation des cookies">Utilisation des
            cookies</a>
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