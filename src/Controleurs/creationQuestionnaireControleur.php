<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'questionnaire.php';

class creationQuestionnaireControleur
{
    function nouveauFormulaire()
    {
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'creation_questionnaire' . DIRECTORY_SEPARATOR . 'creation_questionnaire.php';
    }

    function index()
    {
        $this->nouveauFormulaire();
    }

    function save(){
        $modelQuestionnaire = new questionnaire();
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $user_id = $_SESSION['user_id'];
        $access_pin = rand(100000, 999999);
        $qr_code_token = bin2hex(random_bytes(16));
        while (true) {
           if (!$modelQuestionnaire->exists($access_pin)) {
                break;
            }
            $access_pin = rand(100000, 999999);
        }
        $questions = isset($_POST['questions']) ? json_decode($_POST['questions'], true) : [];
        try {
            $modelQuestionnaire->saveSurvey($user_id, $titre, $description, $access_pin, $qr_code_token, $questions);
            // Redirection ou confirmation (ici on inclut la vue, le JS recevra le HTML 200 OK)
            require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'creation_questionnaire' . DIRECTORY_SEPARATOR . 'creation_questionnaire.php';
        } catch (Exception $e) {
            http_response_code(500);
            echo "Erreur lors de la sauvegarde : " . $e->getMessage();
        }
    }
}
