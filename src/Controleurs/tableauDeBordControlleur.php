<?php

class tableauDeBordControlleur
{

    function index()
    {
        require_once(__DIR__ . '/../Models/questionnaire.php');
        $questionnaireModel = new questionnaire();
        // On suppose que l'ID utilisateur est en session
        $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 1;
        $mesQuestionnaires = $questionnaireModel->getSurveysByUserId($userId);

        require_once(__DIR__ . '/../Views/espace_perso/dashboard.php');
    }

    function getMesQuestionnaires()
    {
        require_once(__DIR__ . '/../Models/questionnaire.php');
        $questionnaireModel = new questionnaire();
        $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
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
        $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

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



    function parametres()
    {
        if (!isset($_GET['id'])) {
            header('Location: ?c=tableauDeBord');
            exit;
        }
        $id = $_GET['id'];
        require_once __DIR__ . '/../Models/questionnaire.php';
        $model = new questionnaire();
        $survey = $model->getSurveyById($id);
        
        if (!$survey) {
             header('Location: ?c=tableauDeBord');
             exit;
        }

        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Parametres' . DIRECTORY_SEPARATOR . 'parametre.php';
    }

    function saveSettings()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Données invalides']);
            exit;
        }

        $id = $data['id'];
        $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
        $acceptResponses = $data['acceptResponses']; // boolean
        
        $status = $acceptResponses ? 'active' : 'closed';
        
        // Other settings can be stored in the JSON column
        $settings = json_encode([
            'dateStart' => $data['dateStart'] ?? null,
            'dateEnd' => $data['dateEnd'] ?? null,
            'notifResponse' => $data['notifResponse'] ?? false,
            'notifLimit' => $data['notifLimit'] ?? false,
            'notifInvalid' => $data['notifInvalid'] ?? false
        ]);

        require_once __DIR__ . '/../Models/questionnaire.php';
        $model = new questionnaire();
        
        if ($model->updateSurveySettings($id, $userId, $status, $settings)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la sauvegarde']);
        }
        exit;
    }



}