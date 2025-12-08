<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionnaire Introuvable - Questionary</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .error-container {
            text-align: center;
            padding: 60px 20px;
        }

        .error-icon {
            font-size: 80px;
            color: var(--secondary);
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .error-title {
            font-size: 2rem;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .error-desc {
            color: var(--gray);
            margin-bottom: 30px;
        }

        .btn-home {
            background-color: #252525;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
    </style>
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
    <div class="app-container" style="justify-content: center; align-items: center;">
        <main class="main-content" style="max-width: 600px; width: 100%;">

            <div class="card error-container">
                <i class="fa-solid fa-file-circle-question error-icon"></i>
                <h1 class="error-title">Oups !</h1>
                <p class="error-desc">Le questionnaire que vous recherchez semble introuvable. Le code PIN est peut-être
                    incorrect ou le questionnaire a été fermé.</p>

                <a href="index.php" class="btn-home">
                    <i class="fa-solid fa-house"></i> Retour à l'accueil
                </a>
            </div>

        </main>
    </div>
</body>

</html>