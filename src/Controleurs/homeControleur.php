<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'questionnaire.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'user.php';
class homeControleur
{
    function valider($pin)
    {

        $modelQuestionnaire = new questionnaire();
        if ($modelQuestionnaire->exists($pin)) {
            $questionQuestionnaire = $modelQuestionnaire->listerLesQuestions($pin);
            require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'qcm' . DIRECTORY_SEPARATOR . 'questionnaireVueEleve.php');
        } else {
            require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'confirmation' . DIRECTORY_SEPARATOR . 'questionnaireNonTrouve.php');
        }

    }

    function index()
    {
        $Model_User = new User();
        $Model_User->createUserIfNotExists($_SESSION['id'], $_SESSION['mail'], $_SESSION['name']);

        if (strpos($_SESSION['mail'], 'etu') !== false) {
            require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Acceuil' . DIRECTORY_SEPARATOR . 'home.php');
        } else {
            header('Location: ?c=tableauDeBord');
        }
    }

    function saveReponse()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['survey_id']) || !isset($data['answers'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données invalides']);
            return;
        }

        $surveyId = $data['survey_id'];
        $answers = $data['answers'];

        require_once __DIR__ . '/../Models/reponse.php';
        $reponseModel = new Reponse();

        if ($reponseModel->saveFullResponse($surveyId, $answers)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la sauvegarde']);
        }
    }
    function merci()
    {
        require_once(__DIR__ . '/../Views/confirmation/merci.php');
    }
}
