<?php

class tableauDeBordControlleur
{

    function index()
    {
        require_once(__DIR__ . '/../Models/questionnaire.php');
        $questionnaireModel = new questionnaire();
        // On suppose que l'ID utilisateur est en session
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
        $mesQuestionnaires = $questionnaireModel->getSurveysByUserId($userId);

        require_once(__DIR__ . '/../Views/espace_perso/dashboard.php');
    }

    function getMesQuestionnaires()
    {
        require_once(__DIR__ . '/../Models/questionnaire.php');
        $questionnaireModel = new questionnaire();
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        $mesQuestionnaires = $questionnaireModel->getSurveysByUserId($userId);

        header('Content-Type: application/json');
        echo json_encode($mesQuestionnaires);
        exit;
    }

    function creerNouveau()
    {

        // TODO (Back-End): Créer en BDD et renvoyer le vrai ID
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success_mock',
            'nouveau_id' => 999
        ]);
        exit;
    }

    function supprimer()
    {
        header('Content-Type: application/json');
        
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

        if (!$id || !$userId) {
             echo json_encode(['status' => 'error', 'message' => 'ID manquant ou utilisateur non connecté']);
             exit;
        }

        require_once(__DIR__ . '/../Models/questionnaire.php');
        $questionnaireModel = new questionnaire();
        
        if ($questionnaireModel->deleteSurvey($id, $userId)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Impossible de supprimer ou questionnaire introuvable']);
        }
        exit;
    }

    function conditionGenerales()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'legal' . DIRECTORY_SEPARATOR . 'conditionGenerales.php';
    }

    function confidentialite()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'legal' . DIRECTORY_SEPARATOR . 'confidentialite.php';
    }

    function parametres()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ?c=tableauDeBord');
            exit;
        }

        require_once(__DIR__ . '/../Models/questionnaire.php');
        $questionnaireModel = new questionnaire();
        $survey = $questionnaireModel->getSurveyById($id);

        if (!$survey) {
            echo "Questionnaire introuvable.";
            return;
        }

        // Add ID to survey array for view to use
        // $survey contains id, title, description, status

        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Parametres' . DIRECTORY_SEPARATOR . 'parametre.php';
    }
    function utilisationCookie()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'legal' . DIRECTORY_SEPARATOR . 'utilisationCookie.php';
    }

}