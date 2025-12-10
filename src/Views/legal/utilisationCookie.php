<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisation des Cookies - Questionary</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <header class="topbar">
        <div class="topbar__left">
            <a href="index.php" class="topbar__logo">
                <span class="appicon" aria-hidden="true"></span>
                <span class="apptitle">QUESTIONARY</span>
            </a>
        </div>
    </header>

    <div class="app-container" style="justify-content: center; padding-top: 40px;">
        <main class="main-content"
            style="max-width: 800px; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <h1 class="section-title">Utilisation des Cookies</h1>
            <p class="last-update">Dernière mise à jour : 10 décembre 2025</p>

            <section class="legal-section">
                <h3>1. Définition d'un Cookie</h3>
                <p>
                    Un "cookie" est un petit fichier texte déposé sur votre terminal (ordinateur, tablette, smartphone) lors de la visite d'un site web. Il permet à son émetteur d'identifier le terminal dans lequel il est enregistré, pendant la durée de validité ou d'enregistrement du cookie.
                </p>
            </section>

            <section class="legal-section">
                <h3>2. Les cookies que nous utilisons</h3>
                <p>
                    Le site <strong>Questionary</strong> utilise exclusivement des cookies dits "techniques" ou "fonctionnels", strictement nécessaires à la fourniture du service. Ils sont exemptés de consentement préalable selon les recommandations de la CNIL.
                </p>
                <div class="cookie-table-container">
                    <table class="cookie-table" style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                        <thead>
                            <tr style="background: #f3f4f6; text-align: left;">
                                <th style="padding: 12px; border: 1px solid #e5e7eb;">Nom du Cookie</th>
                                <th style="padding: 12px; border: 1px solid #e5e7eb;">Finalité</th>
                                <th style="padding: 12px; border: 1px solid #e5e7eb;">Durée de vie</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e5e7eb; font-weight: bold;">PHPSESSID</td>
                                <td style="padding: 12px; border: 1px solid #e5e7eb;">Maintien de la session utilisateur connectée (identifiant de session).</td>
                                <td style="padding: 12px; border: 1px solid #e5e7eb;">Session (expire à la fermeture du navigateur)</td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e5e7eb; font-weight: bold;">TGC / CASPRIVACY</td>
                                <td style="padding: 12px; border: 1px solid #e5e7eb;">Cookies tiers gérés par le serveur CAS de l'université pour l'authentification unique (SSO).</td>
                                <td style="padding: 12px; border: 1px solid #e5e7eb;">Variable (selon paramétrage CAS)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="legal-section">
                <h3>3. Absence de traceurs publicitaires</h3>
                <p>
                    Nous garantissons qu'<strong>aucun cookie publicitaire</strong>, pixel de suivi ou traceur de réseaux sociaux n'est utilisé sur notre plateforme. Vos données de navigation ne sont ni partagées, ni vendues à des tiers commerciaux.
                </p>
            </section>

            <section class="legal-section">
                <h3>4. localStorage et sessionStorage</h3>
                <p>
                    En complément des cookies, nous pouvons utiliser les technologies de stockage local du navigateur (<code>localStorage</code> ou <code>sessionStorage</code>) pour améliorer votre expérience (ex: mémoriser temporairement l'état d'un menu ou un thème d'affichage). Ces données restent strictement sur votre appareil et ne sont pas transférées à des tiers.
                </p>
            </section>

            <section class="legal-section">
                <h3>5. Paramétrage de votre navigateur</h3>
                <p>
                    Vous pouvez à tout moment choisir de désactiver ou bloquer les cookies via les paramètres de votre navigateur. Cependant, nous vous informons que le blocage du cookie <code>PHPSESSID</code> entraînera l'impossibilité de vous connecter à votre espace personnel.
                </p>
                <ul class="legal-list">
                    <li><a href="https://support.google.com/chrome/answer/95647?hl=fr" target="_blank">Google Chrome</a></li>
                    <li><a href="https://support.mozilla.org/fr/kb/protection-renforcee-contre-pistage-firefox-ordinateur" target="_blank">Mozilla Firefox</a></li>
                    <li><a href="https://support.apple.com/fr-fr/guide/safari/sfri11471/mac" target="_blank">Apple Safari</a></li>
                    <li><a href="https://support.microsoft.com/fr-fr/microsoft-edge/supprimer-les-cookies-dans-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank">Microsoft Edge</a></li>
                </ul>
            </section>

            <br>
            <a href="javascript:history.back()" style="color: var(--red); font-weight: 600; text-decoration: none;">&larr; Retour</a>
        </main>
    </div>
</body>

</html>
