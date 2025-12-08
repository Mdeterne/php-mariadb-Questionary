<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'questionnaire.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'user.php';
class homeControleur {
    function valider(){

        $pin = $_POST['pin'];

        $modelQuestionnaire = new questionnaire();
        if($modelQuestionnaire->exists($pin)){
            $questionQuestionnaire = $modelQuestionnaire->listerLesQuestions($pin);
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'qmc'.DIRECTORY_SEPARATOR.'questionnaireVueEleve.php');
        }else{
            require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'confirmation'.DIRECTORY_SEPARATOR.'questionnaireNonTrouve.php');
        }

    }
 
    function index(){
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR.'Acceuil'.DIRECTORY_SEPARATOR.'home.php');
        $_SESSION['user_id'] = 1; // Simuler un utilisateur connecté pour l'exemple
        $_SESSION['user_email'] = 'user@gmail.com';
        $_SESSION['user_name'] = 'Utilisateur Test';
        $Model_User = new User();
        $Model_User->createUserIfNotExists($_SESSION['user_email'], $_SESSION['user_name']);
    }
}
