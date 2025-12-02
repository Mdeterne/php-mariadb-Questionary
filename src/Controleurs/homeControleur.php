<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'questionnaire.php';
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
    }
}
