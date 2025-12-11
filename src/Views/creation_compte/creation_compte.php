<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Questionary - Créer un compte</title>

  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/variables.css" />
  <link rel="stylesheet" href="css/components/topbar.css" />
  <link rel="stylesheet" href="css/components/buttons.css" />
  <link rel="stylesheet" href="css/components/inputs.css" />
  <link rel="stylesheet" href="css/components/cards.css" />
  <link rel="stylesheet" href="css/components/footer.css" />

  <style>
    .error-text {
      color: var(--red);
      font-size: 14px;
      font-weight: 500;
      text-align: center;
      margin-top: -8px;
      margin-bottom: 12px;
      min-height: 1.2em;
    }
  </style>

</head>

<body>
  <?php require_once __DIR__ . '/../components/header.php'; ?>

  <main class="center">
    <section class="card" role="region" aria-labelledby="cardTitle">

      <form method="POST" action="?c=creerUnCompte&a=creerUnCompte">

        <div class="input-wrap">
          <label for="email" class="input-label">Adresse e-mail :</label>
          <input id="email" name="adresse_email" type="text" inputmode="email" placeholder="" autocomplete="email"
            value="<?php echo $username_value ?? ''; ?>" />
          <button class="clear" type="button" aria-label="Effacer l'adresse e-mail">✕</button>
        </div>

        <div class="input-wrap">
          <label for="username" class="input-label">Nom d'utilisateur :</label>
          <input id="username" name="nom_utilisateur" type="text" inputmode="text" placeholder=""
            autocomplete="username" value="<?php echo $username_value ?? ''; ?>" />
          <button class="clear" type="button" aria-label="Effacer le nom d'utilisateur">✕</button>
        </div>

        <div class="input-wrap">
          <label for="password" class="input-label">Mot de passe :</label>
          <input id="password" name="mot_de_passe" type="password" inputmode="text" placeholder=""
            autocomplete="new-password" />
          <button class="clear" type="button" aria-label="Effacer le mot de passe">✕</button>

        </div>

        <div class="input-wrap">
          <label for="password_confirm" class="input-label">Confirmer mot de passe :</label>
          <input id="password_confirm" name="mot_de_passe_confirm" type="password" inputmode="text" placeholder=""
            autocomplete="new-password" /> <button class="clear" type="button"
            aria-label="Effacer le mot de passe">✕</button>
        </div>

        <div id="error-message" class="error-text"></div>

        <button class="btn btn-primary" type="submit">Créer un compte</button>
      </form>
    </section>
  </main>

  <footer class="footer">
    <nav class="footer__links" aria-label="Liens légaux">
      <a href="?c=tableauDeBord&a=conditionGenerales" title="Conditions générales">Conditions générales</a>
      <span aria-hidden="true">|</span>
      <a href="?c=tableauDeBord&a=confidentialite" title="Confidentialité">Confidentialité</a>
      <span aria-hidden="true">|</span>
      <a href="#" title="Utilisation des cookies">Utilisation des cookies</a>
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


      const form = document.querySelector('form');
      const passwordField = document.querySelector('#password');
      const confirmPasswordField = document.querySelector('#password_confirm');
      const errorMessage = document.querySelector('#error-message');

      form.addEventListener('submit', function (event) {

        if (passwordField.value !== confirmPasswordField.value) {

          event.preventDefault();

          errorMessage.textContent = 'Erreur : Les mots de passe ne correspondent pas.';

        } else {

          errorMessage.textContent = '';
        }
      });

      passwordField.addEventListener('input', () => {
        errorMessage.textContent = '';
      });
      confirmPasswordField.addEventListener('input', () => {
        errorMessage.textContent = '';
      });

    });
  </script>

</body>

</html>