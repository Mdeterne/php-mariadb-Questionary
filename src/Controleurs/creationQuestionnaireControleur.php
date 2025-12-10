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
        $modelQuestionnaire->saveSurvey($user_id, $titre, $description, $access_pin, $qr_code_token);
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'espace_perso' . DIRECTORY_SEPARATOR . 'dashboard.php';
    }
}
