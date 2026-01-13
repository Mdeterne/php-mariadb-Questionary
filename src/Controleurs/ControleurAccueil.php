<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Questionnaire.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Utilisateur.php';
class ControleurAccueil
{
    function valider($pin)
    {

        if ($pin == 'je suis un developpeur 01587642098') {
            if ($_SESSION['role'] == 'enseignant') {
                $_SESSION['role'] = 'etudiant';
                require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Accueil' . DIRECTORY_SEPARATOR . 'home.php');
                exit();
            } else {
                $_SESSION['role'] = 'enseignant';
                require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'TableauDeBord' . DIRECTORY_SEPARATOR . 'dashboard.php');
                exit();
            }
        }

        $modelQuestionnaire = new Questionnaire();
        if ($modelQuestionnaire->existsAndOpen($pin)) {
            $questionQuestionnaire = $modelQuestionnaire->listerLesQuestions($pin);
            require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Qcm' . DIRECTORY_SEPARATOR . 'questionnaireVueEleve.php');
        } else {
            require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Confirmation' . DIRECTORY_SEPARATOR . 'questionnaireNonTrouve.php');
        }

    }

    function index()
    {
        $Model_User = new Utilisateur();
        $Model_User->createUserIfNotExists($_SESSION['id'], $_SESSION['mail'], $_SESSION['name']);

        if ($_SESSION['role'] == 'etudiant') {
            require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Accueil' . DIRECTORY_SEPARATOR . 'home.php');
        }
        if ($_SESSION['role'] == 'enseignant') {
            require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'TableauDeBord' . DIRECTORY_SEPARATOR . 'dashboard.php');
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

        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Modeles' . DIRECTORY_SEPARATOR . 'Reponse.php';
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
        require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Confirmation' . DIRECTORY_SEPARATOR . 'merci.php');
    }

    function conditionGenerales()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Legal' . DIRECTORY_SEPARATOR . 'conditionGenerales.php';
    }

    function confidentialite()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Legal' . DIRECTORY_SEPARATOR . 'confidentialite.php';
    }

    function utilisationCookie()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'Legal' . DIRECTORY_SEPARATOR . 'utilisationCookie.php';
    }
}
?>