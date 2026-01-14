<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Questionnaire.php';

class ControleurCreation
{
    /**
     * Affiche le formulaire de création d'un nouveau questionnaire.
     */
    function nouveauFormulaire()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Creation' . DIRECTORY_SEPARATOR . 'creation_questionnaire.php';
    }

    /**
     * Point d'entrée par défaut.
     */
    function index()
    {
        $this->nouveauFormulaire();
    }

    /**
     * Affiche le formulaire d'édition pour un questionnaire existant.
     */
    function editer()
    {
        if (!isset($_GET['id'])) {
            header('Location: ?c=tableauDeBord');
            exit;
        }

        $id = $_GET['id'];
        $modeleQuestionnaire = new Questionnaire();

        // Vérifie la propriété ou l'existence du questionnaire
        // Réutilise la récupération des données d'analyse pour avoir toutes les infos
        $questionnaire = $modeleQuestionnaire->getAnalysisData($id); 
        if (!$questionnaire) {
            header('Location: ?c=tableauDeBord');
            exit;
        }

        // Variable transmise à la vue : $existingSurvey
        $existingSurvey = $questionnaire;
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Creation' . DIRECTORY_SEPARATOR . 'creation_questionnaire.php';
    }

    /**
     * Importe un questionnaire via son code PIN.
     * 
     * @param string $pin Le code PIN du questionnaire à importer.
     */
    function import($pin)
    {
        $modeleQuestionnaire = new Questionnaire();
        if ($modeleQuestionnaire->exists($pin)) {
            $questionnaire = $modeleQuestionnaire->getSurveyByPin($pin);
            // Vérifie si l'utilisateur n'est pas déjà le propriétaire
            if ($questionnaire['user_id'] != $_SESSION['id']) {
                $id = $questionnaire['id'];
                $modeleQuestionnaire->import($id);
                // Redirection vers le tableau de bord avec succès
                header('Location: ?c=tableauDeBord&import=success');
                exit;
            } else {
                // Redirection vers le tableau de bord avec erreur (déjà propriétaire)
                header('Location: ?c=tableauDeBord&import=error');
                exit;
            }
        } else {
            // Redirection vers le tableau de bord avec erreur (introuvable)
            header('Location: ?c=tableauDeBord&import=error');
            exit;
        }
    }

    /**
     * Enregistre un nouveau questionnaire ou met à jour un existant.
     */
    function save()
    {
        $modeleQuestionnaire = new Questionnaire();
        $titre = trim($_POST['titre'] ?? '');
        if (empty($titre)) {
            http_response_code(400);
            echo "Le titre est obligatoire.";
            exit;
        }
        $description = $_POST['description'];
        $idUtilisateur = $_SESSION['id'];
        $questions = isset($_POST['questions']) ? json_decode($_POST['questions'], true) : [];

        // Vérifie s'il s'agit d'une mise à jour
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = $_POST['id'];
            try {
                $modeleQuestionnaire->updateSurvey($id, $idUtilisateur, $titre, $description, $questions);
                require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Creation' . DIRECTORY_SEPARATOR . 'creation_questionnaire.php';
            } catch (Exception $e) {
                http_response_code(500);
                echo "Erreur lors de la mise à jour : " . $e->getMessage();
            }
        } else {
            // Création d'un nouveau questionnaire
            $codePinAcces = rand(100000, 999999);
            $jetonQrCode = bin2hex(random_bytes(16));
            
            // Garantit l'unicité du PIN
            while (true) {
                if (!$modeleQuestionnaire->exists($codePinAcces)) {
                    break;
                }
                $codePinAcces = rand(100000, 999999);
            }
            try {
                $modeleQuestionnaire->saveSurvey($idUtilisateur, $titre, $description, $codePinAcces, $jetonQrCode, $questions);
                // Redirection ou confirmation (ici on inclut la vue, le JS recevra le HTML 200 OK)
                require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Creation' . DIRECTORY_SEPARATOR . 'creation_questionnaire.php';
            } catch (Exception $e) {
                http_response_code(500);
                echo "Erreur lors de la sauvegarde : " . $e->getMessage();
            }
        }
    }
}
